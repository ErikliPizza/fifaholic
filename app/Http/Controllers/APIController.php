<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Models\Matches;
use App\Models\MatchStatistics;
use App\Models\MatchTeam;
use Google\ApiCore\ApiException;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class APIController extends Controller
{
    protected $user;

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();

            if (!$this->user) {
                return new Response('Forbidden', Response::HTTP_FORBIDDEN);
            }

            return $next($request);
        });
    }

    /**
     * Return a welcome response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function welcome()
    {
        return response()->json(['league' => $this->user->leagues], Response::HTTP_ACCEPTED);
    }

    /**
     * Create a match based on the provided request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ApiException
     */
    public function create_match(Request $request)
    {
        /*
         * S1: Get the Request.
         * -> return if League ID or Image is not set AS null, @NO_CONTENT
         * -> return if League ID does not belong to the User AS message, @FORBIDDEN
         *
         * S2: Image Manipulation
         * Manipulate the Top section (H hs : as A)
         * Manipulate the Left section (Home Statistics)
         * Manipulate the Right section (Away Statistics)
         *
         * S3: Main Process.
         * -> return if Top section credentials failure ("H" "hs" : "as" "A") AS message, @BAD_REQUEST
         * -> return if any Team ID does not belong to the User AS message, @FORBIDDEN
         * -> return if Right or Left section credentials failure (Home Statistics, Away Statistics) AS message, @NOT_ACCEPTABLE
         *
         * S4: Create
         * Create the match record.
         *
         * Conclusion:
         * League ID and Extracted Team IDs belongs to user +, in condition
         * HomeTeam and AwayTeam exist in this League +, in condition
         * HomeScore : AwayScore is acceptable from TopSection +, in condition
         * HomeStatistics l| and |r AwayStatistics has exactly 15 data (fifa stat screen has 15 value in total) +, in condition
         * All good to go, insert.
         */

        // Get the Request.
        $leagueId = $request->input('league_id') ?? null; // Get the league ID from the request input
        $imageTaken = $request->file('image') ?? null; // Get the uploaded image file
        if (!$leagueId || !$imageTaken) {
            return response()->json(['message' => 'Check your selected league and try again.'], Response::HTTP_BAD_REQUEST); // Return a response with no content if league ID or image is missing
        }

        $belongsToUser = Auth::user()->leagues()->where('id', $leagueId)->exists(); // Check if the league belongs to the authenticated user
        if (!$belongsToUser) {
            return response()->json(['message' => 'The league does not belong to the user.'], Response::HTTP_FORBIDDEN); // Return a forbidden response if the league doesn't belong to the user
        }

        $currentLeague = League::with('teams')->find($leagueId); // Retrieve the league with its associated teams
        if ($currentLeague->teams->isEmpty()) {
            return response()->json(['message' => 'No team in this league'], Response::HTTP_BAD_REQUEST); // Return a forbidden response if there are no teams in the league
        }

        $teamTitles = [];
        foreach ($currentLeague->teams as $team) {
            $teamTitles[] = $team->getAttributes()['title'];
        }


        // Image Manipulation
        $imageType = exif_imagetype($imageTaken); // Get the type of the uploaded image
        if (!in_array($imageType, [IMAGETYPE_JPEG, IMAGETYPE_PNG])) {
            return response()->json(['message' => 'Unsupported image file.'], Response::HTTP_NOT_ACCEPTABLE); // Return a not acceptable response if the image file type is not supported
        }

        $image = imagecreatefromstring(file_get_contents($imageTaken)); // Create an image resource from the uploaded image file
        if (!$image) {
            return response()->json(['message' => 'Unable to create image from file.'], Response::HTTP_INTERNAL_SERVER_ERROR); // Return an internal server error response if unable to create the image from file
        }

        $image = imagescale($image, 1920, 1080); // Scale the image to a specific size
        $coreWidth = imagesx($image); // Get the width of the image
        $coreHeight = imagesy($image); // Get the height of the image
        // Top Section start
        $topWidth = (int) ($coreWidth * 0.7); // Calculate the width of the top section as 70% of the core width
        $topHeight = (int) ($coreHeight * 0.1 - 10); // Calculate the height of the top section as 10% of the core height minus 10
        $x = (int) (($coreWidth - $topWidth) / 2); // Calculate the x-coordinate for the top section to be horizontally centered
        $y = 3; // Set the y-coordinate for the top section
        $topSection = imagecrop($image, ['x' => $x, 'y' => $y, 'width' => $topWidth, 'height' => $topHeight]); // Crop the image to create the top section

        imagefilter($topSection, IMG_FILTER_BRIGHTNESS, -5); // Apply brightness filter to the top section
        imagefilter($topSection, IMG_FILTER_GRAYSCALE); // Convert the top section to grayscale
        imagefilter($topSection, IMG_FILTER_CONTRAST, -200); // Apply contrast filter to the top section
        imagefilter($topSection, IMG_FILTER_NEGATE); // Negate the colors of the top section

        $whiteAreas = [
            ['x' => 500, 'y' => 10, 'width' => 100, 'height' => 500],
            ['x' => 750, 'y' => 10, 'width' => 100, 'height' => 500],
        ];

        foreach ($whiteAreas as $area) {
            imagefilledrectangle(
                $topSection,
                $area['x'],
                $area['y'],
                $area['x'] + $area['width'],
                $area['y'] + $area['height'],
                imagecolorallocate($topSection, 255, 255, 255)
            ); // Fill the specified areas in the top section with white color
        }

        ob_start();
        imagejpeg($topSection, null, 100); // Output the top section as a JPEG image
        $topBytes = ob_get_clean(); // Get the contents of the output buffer and clear it
        imagedestroy($topSection); // Free up the memory used by the top section image resource
        // Top Section end

        /// Left Section start
        $leftWidth = (int) ($coreWidth * 0.1 - 130); // Calculate the width of the left section as 10% of the core width minus 130
        $leftHeight = (int) ($coreHeight * 0.7); // Calculate the height of the left section as 70% of the core height
        // Create a new GD image resource for the left section
        $leftSection = imagecrop($image, [
            'x' => 660,
            'y' => 225,
            'width' => $leftWidth,
            'height' => $leftHeight
        ]);
        imagefilter($leftSection, IMG_FILTER_GRAYSCALE); // Convert the left section to grayscale
        imagefilter($leftSection, IMG_FILTER_CONTRAST, -100); // Apply contrast filter to the left section
        imagefilter($leftSection, IMG_FILTER_NEGATE); // Negate the colors of the left section
        ob_start();
        imagejpeg($leftSection, null, 100); // Output the left section as a JPEG image
        $leftBytes = ob_get_clean(); // Get the contents of the output buffer and clear it
        imagedestroy($leftSection); // Free up the memory used by the left section image resource
        // Left Section end

        // Right Section start
        $rightWidth = (int) ($coreWidth * 0.1 - 127); // Calculate the width of the right section as 10% of the core width minus 127
        $rightHeight = (int) ($coreHeight * 0.7); // Calculate the height of the right section as 70% of the core height

        $rightSection = imagecrop($image, [
            'x' => 1190,
            'y' => 225,
            'width' => $rightWidth,
            'height' => $rightHeight
        ]); // Create a new GD image resource for the right section

        imagefilter($rightSection, IMG_FILTER_GRAYSCALE); // Convert the right section to grayscale
        imagefilter($rightSection, IMG_FILTER_CONTRAST, -100); // Apply contrast filter to the right section
        imagefilter($rightSection, IMG_FILTER_NEGATE); // Negate the colors of the right section

        ob_start();
        imagejpeg($rightSection, null, 100); // Output the right section as a JPEG image
        $rightBytes = ob_get_clean(); // Get the contents of the output buffer and clear it
        imagedestroy($rightSection); // Free up the memory used by the right section image resource
        // Right Section end
        imagedestroy($image); // Free up the memory used by the core image resource

        $imageAnnotatorClient = new ImageAnnotatorClient();

        $textResponse = $imageAnnotatorClient->textDetection($topBytes); // Perform text detection on the top section image
        $text = $textResponse->getTextAnnotations(); // Get the detected text annotations
        // Main Process
        $modifiedPlantText = '';
        $matchedSections = [];
        $matchedOrder = [];
        $pattern = "/(" . implode("|", array_map('preg_quote', $teamTitles)) . ")/iu"; // Create a regex pattern based on the team titles
        $plantText = $text[0]->getDescription(); // Get the text content from the text annotations

        preg_match_all($pattern, $plantText, $matches, PREG_OFFSET_CAPTURE); // Find matches of the team titles in the text
        foreach ($matches[0] as $match) {
            $matchedSections[] = $match[0]; // Store the matched team title
            $matchedOrder[] = $match[1]; // Store the offset position of the match in the text
        }

        array_multisort($matchedOrder, $matchedSections); // Sort the matched sections based on their offset order/the order they appear in the string
        $matchedSections = array_values(array_unique($matchedSections)); // Remove duplicates and re-index the array
        $modifiedPlantText = str_replace($matchedSections, '', $plantText); // Remove the matched sections from the plant text

        $homeTeam = null;
        $awayTeam = null;

        $teams = $currentLeague->teams()->get(); // Retrieve the teams from the current league

        foreach ($matchedSections as $index => $matchedSection) {
            $filteredItems = $teams->filter(function ($item) use ($matchedSection) {
                $teamTitle = mb_strtolower($item->getRawOriginal('title'), 'UTF-8'); // Get the raw/original title value and convert it to lowercase
                $matchedSection = mb_strtolower($matchedSection, 'UTF-8'); // Convert matched section to lowercase
                return mb_stripos($teamTitle, $matchedSection) !== false; // Perform case-insensitive string search
            });

            $matchingIds = $filteredItems->pluck('id'); // Get the matching team IDs

            if ($index === 0) {
                $homeTeam = $matchingIds->first(); // Set the first matched team as the home team
            } elseif ($index === 1) {
                $awayTeam = $matchingIds->first(); // Set the second matched team as the away team
            }
        }

        $pattern = "/(\d+)\s*:\s*(\d+)/"; // Regular expression pattern for matching scores
        // The pattern matches the following:
        // (\d+) - Matches one or more digits and captures them (represents the home score).
        // \s*   - Matches any number of whitespace characters.
        // :     - Matches the colon character.
        // \s*   - Matches any number of whitespace characters.
        // (\d+) - Matches one or more digits and captures them (represents the away score).

        preg_match($pattern, $modifiedPlantText, $matches); // Perform pattern matching on the modified plant text

        $homeScore = $matches[1] ?? null; // Extract the home score from the matches array
        $awayScore = $matches[2] ?? null; // Extract the away score from the matches array

        if ($homeTeam == null || $homeScore == null || $awayTeam == null || $awayScore == null) {
            return response()->json(['message' => 'Invalid match details. Please make sure the teams added to your account and the titles are has the exact match.'], Response::HTTP_BAD_REQUEST);
        }
        $belongsToUser = Auth::user()->teams()->whereIn('id', [$homeTeam, $awayTeam])->exists();
        if (!$belongsToUser) {
            return response()->json(['message' => 'The team does not belong to the user.'], Response::HTTP_FORBIDDEN);
        }
        $homeStats = $this->processImageStats($leftBytes, $imageAnnotatorClient);
        $awayStats = $this->processImageStats($rightBytes, $imageAnnotatorClient);

        if (count($homeStats) !== 15 || count($awayStats) !== 15) {
            return response()->json(['message' => 'Sorry, statistic credentials could not be recognized well. :('], Response::HTTP_BAD_REQUEST);
        }

        // Create

        // Create a new match record
        $matchData = [
            'league_id' => $leagueId,
            'home_team_id' => $homeTeam,
            'away_team_id' => $awayTeam,
            'week' => 0,
            'home_team_score' => $homeScore,
            'away_team_score' => $awayScore
        ];
        $match = Matches::create($matchData);
        // Create two new match_team records
        $matchTeamsData = [
            [
                'match_id' => $match->id,
                'team_id' => $homeTeam,
                'home_or_away' => 'home'
            ],
            [
                'match_id' => $match->id,
                'team_id' => $awayTeam,
                'home_or_away' => 'away'
            ]
        ];
        MatchTeam::insert($matchTeamsData);
        $stats = [
            [
                'team_id' => $homeTeam,
                'stats' => $homeStats
            ],
            [
                'team_id' => $awayTeam,
                'stats' => $awayStats
            ]
        ];

        $matchStatisticsData = [];
        foreach ($stats as $stat) {
            $matchStatisticsData[] = [
                'match_id' => $match->id,
                'team_id' => $stat['team_id'],
                'possession' => $stat['stats'][0],
                'shots' => $stat['stats'][1],
                'expected_goals' => $stat['stats'][2],
                'passes' => $stat['stats'][3],
                'tackles' => $stat['stats'][4],
                'tackles_won' => $stat['stats'][5],
                'interceptions' => $stat['stats'][6],
                'saves' => $stat['stats'][7],
                'fouls_committed' => $stat['stats'][8],
                'offsides' => $stat['stats'][9],
                'corners' => $stat['stats'][10],
                'free_kicks' => $stat['stats'][11],
                'penalty_kicks' => $stat['stats'][12],
                'yellow_cards' => $stat['stats'][13],
                'red_cards' => $stat['stats'][14]
            ];
        }
        MatchStatistics::insert($matchStatisticsData);
        // Return a JSON response with a success message
        return response()->json(['message' => 'Successfully created!'], Response::HTTP_CREATED);
    }

    /**
     * Process the image data and extract statistics from it using the provided image annotator client.
     *
     * @param mixed $imageData The image data to process.
     * @param mixed $imageAnnotatorClient The image annotator client to use for text detection.
     * @return array The extracted statistics from the image.
     * @throws ApiException
     */
    protected function processImageStats(mixed $imageData, ImageAnnotatorClient $imageAnnotatorClient): array
    {
        $textResponse = $imageAnnotatorClient->textDetection($imageData);
        $text = $textResponse->getTextAnnotations();
        $stats = [];
        $pattern = '/\d+(\.\d+)?/';
        foreach ($text as $index => $annotation) {
            if ($index === 0) {
                continue;
            }
            $t = $annotation->getDescription();
            preg_match($pattern, $t, $matches);
            if (!empty($matches)) {
                $stats[] = $matches[0];
            }
        }
        return $stats;
    }



}
