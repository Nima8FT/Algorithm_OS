<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageReplacementController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/fifo",
     *     summary="Simulate FIFO Page Replacement Algorithm",
     *     description="This API simulates the FIFO (First-In-First-Out) page replacement algorithm by accepting a sequence of page references and the number of frames.",
     *     tags={"Page Replacement"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *
     *             @OA\Schema(
     *                 type="object",
     *                 required={"Algorithm", "Refrences", "Frames"},
     *
     *                 @OA\Property(
     *                     property="Algorithm",
     *                     type="string",
     *                     description="Type of page replacement algorithm (e.g., FIFO)",
     *                     example="FIFO"
     *                 ),
     *                 @OA\Property(
     *                     property="Refrences",
     *                     type="string",
     *                     description="A sequence of page references (space-separated integers)",
     *                     example="1 3 0 3 5 6 3 5 6 7 3 0"
     *                 ),
     *                 @OA\Property(
     *                     property="Frames",
     *                     type="integer",
     *                     description="The number of frames available in memory",
     *                     example=3
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="pageFaults",
     *                 type="integer",
     *                 description="The number of page faults occurred during the algorithm"
     *             ),
     *             @OA\Property(
     *                 property="pageFaultRate",
     *                 type="number",
     *                 format="float",
     *                 description="The page fault rate calculated as a percentage"
     *             ),
     *             @OA\Property(
     *                 property="frameHistory",
     *                 type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="frameState", type="array", @OA\Items(type="integer", nullable=true)),
     *                     @OA\Property(property="page", type="integer", description="Page referenced in this step"),
     *                     @OA\Property(property="fault", type="boolean", description="Whether a page fault occurred at this step")
     *                 ),
     *                 description="The state of frames after each page reference"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request data"
     *     )
     * )
     */
    public function fifo(Request $request)
    {
        if ($request->input('Algorithm') == 'FIFO') {
            $frames = $request->get('Frames');
            $refrences = explode(' ', $request->get('Refrences'));

            $j = 0;
            $frame = [];
            $page_fault = 0;
            $chart = [];
            $counter = 0;

            while ($j < count($refrences)) {

                $is_process = 0;

                if (count($frame) == $frames) {
                    for ($i = 0; $i < $frames; $i++) {
                        if ($refrences[$j] !== $frame[$i]) {
                            $is_process = 1;
                        } else {
                            $is_process = 0;
                            $chart[] = [
                                'process' => $refrences[$j],
                                'frame' => $frame,
                                'page fault' => 'hit',
                            ];
                            break;
                        }
                    }

                    if ($is_process == 1) {
                        if ($counter >= $frames) {
                            $counter = 0;
                        }
                        $frame[$counter] = $refrences[$j];
                        $counter++;
                        $page_fault++;
                        $chart[] = [
                            'process' => $refrences[$j],
                            'frame' => $frame,
                            'page fault' => '*',
                        ];
                    }
                } else {
                    array_push($frame, $refrences[$j]);
                    $page_fault++;
                    $chart[] = [
                        'process' => $refrences[$j],
                        'frame' => $frame,
                        'page fault' => '*',
                    ];
                }

                $j++;
            }

            return response()->json([
                'chart' => $chart,
                'page_fault' => $page_fault,
            ]);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/lru",
     *     summary="Simulate LRU Page Replacement Algorithm",
     *     description="This API simulates the LRU (Last Recently Used) page replacement algorithm by accepting a sequence of page references and the number of frames.",
     *     tags={"Page Replacement"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *
     *             @OA\Schema(
     *                 type="object",
     *                 required={"Algorithm", "Refrences", "Frames"},
     *
     *                 @OA\Property(
     *                     property="Algorithm",
     *                     type="string",
     *                     description="Type of page replacement algorithm (e.g., LRU)",
     *                     example="LRU"
     *                 ),
     *                 @OA\Property(
     *                     property="Refrences",
     *                     type="string",
     *                     description="A sequence of page references (space-separated integers)",
     *                     example="1 3 0 3 5 6 3 5 6 7 3 0"
     *                 ),
     *                 @OA\Property(
     *                     property="Frames",
     *                     type="integer",
     *                     description="The number of frames available in memory",
     *                     example=3
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="pageFaults",
     *                 type="integer",
     *                 description="The number of page faults occurred during the algorithm"
     *             ),
     *             @OA\Property(
     *                 property="pageFaultRate",
     *                 type="number",
     *                 format="float",
     *                 description="The page fault rate calculated as a percentage"
     *             ),
     *             @OA\Property(
     *                 property="frameHistory",
     *                 type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="frameState", type="array", @OA\Items(type="integer", nullable=true)),
     *                     @OA\Property(property="page", type="integer", description="Page referenced in this step"),
     *                     @OA\Property(property="fault", type="boolean", description="Whether a page fault occurred at this step")
     *                 ),
     *                 description="The state of frames after each page reference"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request data"
     *     )
     * )
     */
    public function lru(Request $request)
    {
        if ($request->input('Algorithm') == 'LRU') {
            $frames = $request->get('Frames');
            $refrences = explode(' ', $request->get('Refrences'));

            $j = 0;
            $frame = [];
            $page_fault = 0;
            $chart = [];
            $id_frame = -1;

            while ($j < count($refrences)) {

                $is_frame = array_fill(0, $frames, false);
                $is_process = 0;
                $count = 0;

                if (count($frame) == $frames) {
                    for ($i = 0; $i < $frames; $i++) {
                        if ($refrences[$j] !== $frame[$i]) {
                            $is_process = 1;
                        } else {
                            $is_process = 0;
                            $chart[] = [
                                'process' => $refrences[$j],
                                'frame' => $frame,
                                'page fault' => 'hit',
                            ];
                            break;
                        }
                    }

                    if ($is_process) {
                        for ($i = $j - 1; $i >= 0; $i--) {
                            if (count($frame) == $frames) {
                                for ($k = 0; $k < $frames; $k++) {
                                    if ($refrences[$i] == $frame[$k] && ! $is_frame[$k]) {
                                        $count++;
                                        $id_frame = $k;
                                        $is_frame[$k] = true;
                                    }
                                }
                            }

                            if ($count == $frames) {
                                $frame[$id_frame] = $refrences[$j];
                                $page_fault++;
                                $chart[] = [
                                    'process' => $refrences[$j],
                                    'frame' => $frame,
                                    'page fault' => '*',
                                ];
                                break;
                            }
                        }
                    }
                } else {
                    array_push($frame, $refrences[$j]);
                    $page_fault++;
                    $chart[] = [
                        'process' => $refrences[$j],
                        'frame' => $frame,
                        'page fault' => '*',
                    ];
                }
                $j++;
            }

            return response()->json([
                'chart' => $chart,
                'page_fault' => $page_fault,
            ]);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/mru",
     *     summary="Simulate MRU Page Replacement Algorithm",
     *     description="This API simulates the MRU (Most Recently Used) page replacement algorithm by accepting a sequence of page references and the number of frames.",
     *     tags={"Page Replacement"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *
     *             @OA\Schema(
     *                 type="object",
     *                 required={"Algorithm", "Refrences", "Frames"},
     *
     *                 @OA\Property(
     *                     property="Algorithm",
     *                     type="string",
     *                     description="Type of page replacement algorithm (e.g., MRU)",
     *                     example="MRU"
     *                 ),
     *                 @OA\Property(
     *                     property="Refrences",
     *                     type="string",
     *                     description="A sequence of page references (space-separated integers)",
     *                     example="1 3 0 3 5 6 3 5 6 7 3 0"
     *                 ),
     *                 @OA\Property(
     *                     property="Frames",
     *                     type="integer",
     *                     description="The number of frames available in memory",
     *                     example=3
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="pageFaults",
     *                 type="integer",
     *                 description="The number of page faults occurred during the algorithm"
     *             ),
     *             @OA\Property(
     *                 property="pageFaultRate",
     *                 type="number",
     *                 format="float",
     *                 description="The page fault rate calculated as a percentage"
     *             ),
     *             @OA\Property(
     *                 property="frameHistory",
     *                 type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="frameState", type="array", @OA\Items(type="integer", nullable=true)),
     *                     @OA\Property(property="page", type="integer", description="Page referenced in this step"),
     *                     @OA\Property(property="fault", type="boolean", description="Whether a page fault occurred at this step")
     *                 ),
     *                 description="The state of frames after each page reference"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request data"
     *     )
     * )
     */
    public function mru(Request $request)
    {
        if ($request->input('Algorithm') == 'MRU') {
            $frames = $request->get('Frames');
            $refrences = explode(' ', $request->get('Refrences'));

            $j = 0;
            $frame = [];
            $page_fault = 0;
            $chart = [];

            while ($j < count($refrences)) {

                $is_process = 0;

                if (count($frame) == $frames) {
                    for ($i = 0; $i < $frames; $i++) {
                        if ($refrences[$j] !== $frame[$i]) {
                            $is_process = 1;
                        } else {
                            $is_process = 0;
                            $chart[] = [
                                'process' => $refrences[$j],
                                'frame' => $frame,
                                'page fault' => 'hit',
                            ];
                            break;
                        }
                    }

                    if ($is_process) {
                        $page_fault++;
                        for ($i = 0; $i < $frames; $i++) {
                            if ($refrences[$j - 1] == $frame[$i]) {
                                $frame[$i] = $refrences[$j];
                            }
                        }
                        $chart[] = [
                            'process' => $refrences[$j],
                            'frame' => $frame,
                            'page fault' => '*',
                        ];
                    }
                } else {
                    array_push($frame, $refrences[$j]);
                    $page_fault++;
                    $chart[] = [
                        'process' => $refrences[$j],
                        'frame' => $frame,
                        'page fault' => '*',
                    ];
                }
                $j++;
            }

            return response()->json([
                'chart' => $chart,
                'page_fault' => $page_fault,
            ]);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/lifo",
     *     summary="Simulate LIFO Page Replacement Algorithm",
     *     description="This API simulates the LIFO (Last In First Out) page replacement algorithm by accepting a sequence of page references and the number of frames.",
     *     tags={"Page Replacement"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *
     *             @OA\Schema(
     *                 type="object",
     *                 required={"Algorithm", "Refrences", "Frames"},
     *
     *                 @OA\Property(
     *                     property="Algorithm",
     *                     type="string",
     *                     description="Type of page replacement algorithm (e.g., LIFO)",
     *                     example="LIFO"
     *                 ),
     *                 @OA\Property(
     *                     property="Refrences",
     *                     type="string",
     *                     description="A sequence of page references (space-separated integers)",
     *                     example="1 3 0 3 5 6 3 5 6 7 3 0"
     *                 ),
     *                 @OA\Property(
     *                     property="Frames",
     *                     type="integer",
     *                     description="The number of frames available in memory",
     *                     example=3
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="pageFaults",
     *                 type="integer",
     *                 description="The number of page faults occurred during the algorithm"
     *             ),
     *             @OA\Property(
     *                 property="pageFaultRate",
     *                 type="number",
     *                 format="float",
     *                 description="The page fault rate calculated as a percentage"
     *             ),
     *             @OA\Property(
     *                 property="frameHistory",
     *                 type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="frameState", type="array", @OA\Items(type="integer", nullable=true)),
     *                     @OA\Property(property="page", type="integer", description="Page referenced in this step"),
     *                     @OA\Property(property="fault", type="boolean", description="Whether a page fault occurred at this step")
     *                 ),
     *                 description="The state of frames after each page reference"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request data"
     *     )
     * )
     */
    public function lifo(Request $request)
    {
        if ($request->input('Algorithm') == 'LIFO') {
            $frames = $request->get('Frames');
            $refrences = explode(' ', $request->get('Refrences'));

            $j = 0;
            $frame = [];
            $page_fault = 0;
            $chart = [];
            // $counter = 0;

            while ($j < count($refrences)) {

                $is_process = 0;

                if (count($frame) == $frames) {
                    for ($i = 0; $i < $frames; $i++) {
                        if ($refrences[$j] !== $frame[$i]) {
                            $is_process = 1;
                        } else {
                            $is_process = 0;
                            $chart[] = [
                                'process' => $refrences[$j],
                                'frame' => $frame,
                                'page fault' => 'hit',
                            ];
                            break;
                        }
                    }

                    if ($is_process) {
                        $page_fault++;
                        for ($i = $j - 1; $i >= 0; $i--) {
                            if (count($frame) == $frames) {
                                for ($k = 0; $k < $frames; $k++) {
                                    if ($refrences[$i] == $frame[$k] && $chart[$i]['page fault'] == '*') {
                                        unset($frame[$k]);
                                        break;
                                    }
                                }
                            } else {
                                break;
                            }
                        }
                        array_push($frame, $refrences[$j]);
                        $frame = array_values($frame);
                        $chart[] = [
                            'process' => $refrences[$j],
                            'frame' => $frame,
                            'page fault' => '*',
                        ];
                    }
                } else {
                    array_push($frame, $refrences[$j]);
                    $page_fault++;
                    $chart[] = [
                        'process' => $refrences[$j],
                        'frame' => $frame,
                        'page fault' => '*',
                    ];
                }
                $j++;
            }

            return response()->json([
                'chart' => $chart,
                'page_fault' => $page_fault,
            ]);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/lfu",
     *     summary="Simulate LFU Page Replacement Algorithm",
     *     description="This API simulates the LFU (Least Frequently Used) page replacement algorithm by accepting a sequence of page references and the number of frames.",
     *     tags={"Page Replacement"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *
     *             @OA\Schema(
     *                 type="object",
     *                 required={"Algorithm", "Refrences", "Frames"},
     *
     *                 @OA\Property(
     *                     property="Algorithm",
     *                     type="string",
     *                     description="Type of page replacement algorithm (e.g., LFU)",
     *                     example="LFU"
     *                 ),
     *                 @OA\Property(
     *                     property="Refrences",
     *                     type="string",
     *                     description="A sequence of page references (space-separated integers)",
     *                     example="1 3 0 3 5 6 3 5 6 7 3 0"
     *                 ),
     *                 @OA\Property(
     *                     property="Frames",
     *                     type="integer",
     *                     description="The number of frames available in memory",
     *                     example=3
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="pageFaults",
     *                 type="integer",
     *                 description="The number of page faults occurred during the algorithm"
     *             ),
     *             @OA\Property(
     *                 property="pageFaultRate",
     *                 type="number",
     *                 format="float",
     *                 description="The page fault rate calculated as a percentage"
     *             ),
     *             @OA\Property(
     *                 property="frameHistory",
     *                 type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="frameState", type="array", @OA\Items(type="integer", nullable=true)),
     *                     @OA\Property(property="page", type="integer", description="Page referenced in this step"),
     *                     @OA\Property(property="fault", type="boolean", description="Whether a page fault occurred at this step")
     *                 ),
     *                 description="The state of frames after each page reference"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request data"
     *     )
     * )
     */
    public function lfu(Request $request)
    {
        if ($request->input('Algorithm') == 'LFU') {
            $frames = $request->get('Frames');
            $refrences = explode(' ', $request->get('Refrences'));
            $refrences_unique = array_unique($refrences);
            $refrences_unique = array_values($refrences_unique);
            $refrences_frequence = array_fill(0, count($refrences_unique), 0);
            $refrences_unique_array = array_combine($refrences_unique, $refrences_frequence);

            $j = 0;
            $frame = [];
            $show_frame = [];
            $page_fault = 0;
            $chart = [];

            while ($j < count($refrences)) {
                $current = $refrences[$j];

                if (in_array($current, $frame)) {
                    $refrences_unique_array[$current]++;
                    $chart[] = [
                        'process' => $current,
                        'frame' => $show_frame,
                        'page fault' => 'hit',
                    ];
                } else {
                    if (count($frame) < $frames) {
                        $frame[] = $current;
                        $show_frame = $frame;
                    } else {
                        $lfu_page = null;
                        $min_freq = PHP_INT_MAX;

                        foreach ($frame as $page) {
                            if ($refrences_unique_array[$page] < $min_freq) {
                                $min_freq = $refrences_unique_array[$page];
                                $lfu_page = $page;
                            }
                        }

                        $key = array_search($lfu_page, $frame);
                        $key_show_frame = array_search($frame[$key], $show_frame);
                        $refrences_unique_array[$frame[$key]]--;
                        unset($frame[$key]);
                        $frame = array_values($frame);
                        $frame[] = $current;
                        $show_frame[$key_show_frame] = $current;
                    }

                    $refrences_unique_array[$current]++;
                    $page_fault++;
                    $chart[] = [
                        'process' => $current,
                        'frame' => $show_frame,
                        'page fault' => '*',
                    ];
                }

                $j++;
            }

            return response()->json([
                'chart' => $chart,
                'page_fault' => $page_fault,
            ]);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/mfu",
     *     summary="Simulate MFU Page Replacement Algorithm",
     *     description="This API simulates the MFU (Most Frequently Used) page replacement algorithm by accepting a sequence of page references and the number of frames.",
     *     tags={"Page Replacement"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *
     *             @OA\Schema(
     *                 type="object",
     *                 required={"Algorithm", "Refrences", "Frames"},
     *
     *                 @OA\Property(
     *                     property="Algorithm",
     *                     type="string",
     *                     description="Type of page replacement algorithm (e.g., MFU)",
     *                     example="MFU"
     *                 ),
     *                 @OA\Property(
     *                     property="Refrences",
     *                     type="string",
     *                     description="A sequence of page references (space-separated integers)",
     *                     example="1 3 0 3 5 6 3 5 6 7 3 0"
     *                 ),
     *                 @OA\Property(
     *                     property="Frames",
     *                     type="integer",
     *                     description="The number of frames available in memory",
     *                     example=3
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="pageFaults",
     *                 type="integer",
     *                 description="The number of page faults occurred during the algorithm"
     *             ),
     *             @OA\Property(
     *                 property="pageFaultRate",
     *                 type="number",
     *                 format="float",
     *                 description="The page fault rate calculated as a percentage"
     *             ),
     *             @OA\Property(
     *                 property="frameHistory",
     *                 type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="frameState", type="array", @OA\Items(type="integer", nullable=true)),
     *                     @OA\Property(property="page", type="integer", description="Page referenced in this step"),
     *                     @OA\Property(property="fault", type="boolean", description="Whether a page fault occurred at this step")
     *                 ),
     *                 description="The state of frames after each page reference"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request data"
     *     )
     * )
     */
    public function mfu(Request $request)
    {
        if ($request->input('Algorithm') == 'MFU') {
            $frames = $request->get('Frames');
            $refrences = explode(' ', $request->get('Refrences'));
            $refrences_unique = array_unique($refrences);
            $refrences_unique = array_values($refrences_unique);
            $refrences_frequence = array_fill(0, count($refrences_unique), 0);
            $refrences_unique_array = array_combine($refrences_unique, $refrences_frequence);

            $j = 0;
            $frame = [];
            $show_frame = [];
            $page_fault = 0;
            $chart = [];

            while ($j < count($refrences)) {
                $current = $refrences[$j];

                if (in_array($current, $frame)) {
                    $refrences_unique_array[$current]++;
                    $chart[] = [
                        'process' => $current,
                        'frame' => $show_frame,
                        'page fault' => 'hit',
                    ];
                } else {
                    if (count($frame) < $frames) {
                        $frame[] = $current;
                        $show_frame = $frame;
                    } else {
                        $lfu_page = null;
                        $high_freq = -1;

                        foreach ($frame as $page) {
                            if ($refrences_unique_array[$page] > $high_freq) {
                                $high_freq = $refrences_unique_array[$page];
                                $lfu_page = $page;
                            }
                        }

                        $key = array_search($lfu_page, $frame);
                        $key_show_frame = array_search($frame[$key], $show_frame);
                        $refrences_unique_array[$frame[$key]] = 0;
                        unset($frame[$key]);
                        $frame = array_values($frame);
                        $frame[] = $current;
                        $show_frame[$key_show_frame] = $current;
                    }

                    $refrences_unique_array[$current]++;
                    $page_fault++;
                    $chart[] = [
                        'process' => $current,
                        'frame' => $show_frame,
                        'page fault' => '*',
                    ];
                }

                $j++;
            }

            return response()->json([
                'chart' => $chart,
                'page_fault' => $page_fault,
            ]);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/randompagereplacement",
     *     summary="Simulate Random Page Replacement Page Replacement Algorithm",
     *     description="This API simulates the Random Page Replacement page replacement algorithm by accepting a sequence of page references and the number of frames.",
     *     tags={"Page Replacement"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *
     *             @OA\Schema(
     *                 type="object",
     *                 required={"Algorithm", "Refrences", "Frames"},
     *
     *                 @OA\Property(
     *                     property="Algorithm",
     *                     type="string",
     *                     description="Type of page replacement algorithm (e.g., Random Page Replacement)",
     *                     example="Random Page Replacement"
     *                 ),
     *                 @OA\Property(
     *                     property="Refrences",
     *                     type="string",
     *                     description="A sequence of page references (space-separated integers)",
     *                     example="1 3 0 3 5 6 3 5 6 7 3 0"
     *                 ),
     *                 @OA\Property(
     *                     property="Frames",
     *                     type="integer",
     *                     description="The number of frames available in memory",
     *                     example=3
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="pageFaults",
     *                 type="integer",
     *                 description="The number of page faults occurred during the algorithm"
     *             ),
     *             @OA\Property(
     *                 property="pageFaultRate",
     *                 type="number",
     *                 format="float",
     *                 description="The page fault rate calculated as a percentage"
     *             ),
     *             @OA\Property(
     *                 property="frameHistory",
     *                 type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="frameState", type="array", @OA\Items(type="integer", nullable=true)),
     *                     @OA\Property(property="page", type="integer", description="Page referenced in this step"),
     *                     @OA\Property(property="fault", type="boolean", description="Whether a page fault occurred at this step")
     *                 ),
     *                 description="The state of frames after each page reference"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request data"
     *     )
     * )
     */
    public function rendomPageReplacement(Request $request)
    {
        if ($request->input('Algorithm') == 'Random Page Replacement') {
            $frames = $request->get('Frames');
            $refrences = explode(' ', $request->get('Refrences'));

            $j = 0;
            $frame = [];
            $chart = [];
            $page_fault = 0;

            while ($j < count($refrences)) {
                if (in_array($refrences[$j], $frame)) {
                    $chart[] = [
                        'process' => $refrences[$j],
                        'frame' => $frame,
                        'page fault' => 'hit',
                    ];
                } else {
                    if (count($frame) < $frames) {
                        $frame[] = $refrences[$j];
                    } else {
                        $random = array_rand($frame);
                        unset($frame[$random]);
                        $frame[] = $refrences[$j];
                        $frame = array_values($frame);
                    }
                    $page_fault++;
                    $chart[] = [
                        'process' => $refrences[$j],
                        'frame' => $frame,
                        'page fault' => '*',
                    ];
                }
                $j++;
            }

            return response()->json([
                'chart' => $chart,
                'page_fault' => $page_fault,
            ]);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/optimalpagereplacement",
     *     summary="Simulate Optimal Page Replacement Page Replacement Algorithm",
     *     description="This API simulates the Optimal Page Replacement page replacement algorithm by accepting a sequence of page references and the number of frames.",
     *     tags={"Page Replacement"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *
     *             @OA\Schema(
     *                 type="object",
     *                 required={"Algorithm", "Refrences", "Frames"},
     *
     *                 @OA\Property(
     *                     property="Algorithm",
     *                     type="string",
     *                     description="Type of page replacement algorithm (e.g., Optimal Page Replacement)",
     *                     example="Optimal Page Replacement"
     *                 ),
     *                 @OA\Property(
     *                     property="Refrences",
     *                     type="string",
     *                     description="A sequence of page references (space-separated integers)",
     *                     example="1 3 0 3 5 6 3 5 6 7 3 0"
     *                 ),
     *                 @OA\Property(
     *                     property="Frames",
     *                     type="integer",
     *                     description="The number of frames available in memory",
     *                     example=3
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="pageFaults",
     *                 type="integer",
     *                 description="The number of page faults occurred during the algorithm"
     *             ),
     *             @OA\Property(
     *                 property="pageFaultRate",
     *                 type="number",
     *                 format="float",
     *                 description="The page fault rate calculated as a percentage"
     *             ),
     *             @OA\Property(
     *                 property="frameHistory",
     *                 type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="frameState", type="array", @OA\Items(type="integer", nullable=true)),
     *                     @OA\Property(property="page", type="integer", description="Page referenced in this step"),
     *                     @OA\Property(property="fault", type="boolean", description="Whether a page fault occurred at this step")
     *                 ),
     *                 description="The state of frames after each page reference"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request data"
     *     )
     * )
     */
    public function optimalPageReplacement(Request $request)
    {
        if ($request->input('Algorithm') == 'Optimal Page Replacement') {
            $frames = $request->get('Frames');
            $refrences = explode(' ', $request->get('Refrences'));

            $j = 0;
            $frame = [];
            $show_frame = [];
            $chart = [];
            $page_fault = 0;

            while ($j < count($refrences)) {
                $current = $refrences[$j];

                if (in_array($current, $frame)) {
                    $chart[] = [
                        'process' => $current,
                        'frame' => $show_frame,
                        'page fault' => 'hit',
                    ];
                } else {
                    if (count($frame) < $frames) {
                        $frame[] = $current;
                        $show_frame = $frame;
                    } else {
                        $farthest = -1;
                        $index_to_replace = -1;

                        foreach ($frame as $index => $page) {
                            $found = false;
                            for ($k = $j + 1; $k < count($refrences); $k++) {
                                if ($page == $refrences[$k]) {
                                    if ($k > $farthest) {
                                        $farthest = $k;
                                        $index_to_replace = $index;
                                    }
                                    $found = true;
                                    break;
                                }
                            }
                            if (! $found) {
                                $index_to_replace = $index;
                                break;
                            }
                        }

                        if ($index_to_replace == -1) {
                            $index_to_replace = 0;
                        }

                        $key = array_search($frame[$index_to_replace], $show_frame);
                        unset($frame[$index_to_replace]);
                        $frame[] = $current;
                        $frame = array_values($frame);
                        $show_frame[$key] = $current;
                    }
                    $page_fault++;
                    $chart[] = [
                        'process' => $current,
                        'frame' => $show_frame,
                        'page fault' => '*',
                    ];
                }
                $j++;
            }

            return response()->json([
                'chart' => $chart,
                'page_fault' => $page_fault,
            ]);

        }
    }
}
