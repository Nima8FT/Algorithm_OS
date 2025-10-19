<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MemoryController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/bestfit",
     *     summary="Simulate Best Fit Memory Allocation Algorithm",
     *     description="This API simulates the Best Fit memory allocation algorithm by accepting block sizes and process sizes to allocate memory efficiently.",
     *     tags={"Memory Allocation"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *
     *             @OA\Schema(
     *                 type="object",
     *                 required={"Block", "Process"},
     *
     *                 @OA\Property(
     *                     property="Algorithm",
     *                     type="string",
     *                     description="Type of algorithm (e.g., bestfit)",
     *                     example="bestfit"
     *                 ),
     *                 @OA\Property(
     *                     property="Block",
     *                     type="string",
     *                     description="Sizes of memory blocks (space-separated)",
     *                     example="100 500 200 300 600"
     *                 ),
     *                 @OA\Property(
     *                     property="Process",
     *                     type="string",
     *                     description="Sizes of processes (space-separated)",
     *                     example="212 417 112 426"
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation with memory allocation results"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request due to incorrect input"
     *     )
     * )
     */
    public function best_fit(Request $request)
    {
        if ($request->input('Algorithm') == 'bestfit') {
            $block_array = explode(' ', $request->get('Block'));
            $process_array = explode(' ', $request->get('Process'));

            $j = 0;
            $processes = [];
            $chart = [];

            while ($j < count($block_array)) {
                $shortest_process = PHP_INT_MAX;
                $process_num = -1;

                for ($i = 0; $i < count($process_array); $i++) {
                    if ($block_array[$j] <= $process_array[$i] && $process_array[$i] <= $shortest_process) {
                        $shortest_process = $process_array[$i];
                        $process_num = $i;
                    }
                }

                if ($process_num != -1) {
                    $processes[] = [
                        'process' => $j + 1,
                        'process_size' => $process_array[$process_num],
                        'block_size' => $block_array[$j],
                        'left_over' => $process_array[$process_num] -= $block_array[$j],
                    ];
                    $chart[] = [
                        'process' => $j + 1,
                        'chart' => $process_array,
                    ];
                }
                $j++;
            }

            return response()->json(
                [
                    'process' => $processes,
                    'chart' => $chart,
                ],
            );
        }
    }

    /**
     * @OA\Post(
     *     path="/api/firstfit",
     *     summary="Simulate First Fit Memory Allocation Algorithm",
     *     description="This API simulates the First Fit memory allocation algorithm by accepting block sizes and process sizes to allocate memory efficiently.",
     *     tags={"Memory Allocation"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *
     *             @OA\Schema(
     *                 type="object",
     *                 required={"Block", "Process"},
     *
     *                 @OA\Property(
     *                     property="Algorithm",
     *                     type="string",
     *                     description="Type of algorithm (e.g., firstfit)",
     *                     example="firstfit"
     *                 ),
     *                 @OA\Property(
     *                     property="Block",
     *                     type="string",
     *                     description="Sizes of memory blocks (space-separated)",
     *                     example="100 500 200 300 600"
     *                 ),
     *                 @OA\Property(
     *                     property="Process",
     *                     type="string",
     *                     description="Sizes of processes (space-separated)",
     *                     example="212 417 112 426"
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation with memory allocation results"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request due to incorrect input"
     *     )
     * )
     */
    public function first_fit(Request $request)
    {
        if ($request->input('Algorithm') == 'firstfit') {
            $block_array = explode(' ', $request->get('Block'));
            $process_array = explode(' ', $request->get('Process'));

            $j = 0;
            $processes = [];
            $chart = [];

            while ($j < count($block_array)) {
                $process_num = -1;

                for ($i = 0; $i < count($process_array); $i++) {
                    if ($block_array[$j] <= $process_array[$i]) {
                        $process_num = $i;
                        break;
                    }
                }

                if ($process_num != -1) {
                    $processes[] = [
                        'process' => $j + 1,
                        'process_size' => $process_array[$process_num],
                        'block_size' => $block_array[$j],
                        'left_over' => $process_array[$process_num] -= $block_array[$j],
                    ];
                    $chart[] = [
                        'process' => $j + 1,
                        'chart' => $process_array,
                    ];
                }
                $j++;
            }

            return response()->json(
                [
                    'process' => $processes,
                    'chart' => $chart,
                ],
            );
        }
    }

    /**
     * @OA\Post(
     *     path="/api/worstfit",
     *     summary="Simulate First Fit Memory Allocation Algorithm",
     *     description="This API simulates the Worst Fit memory allocation algorithm by accepting block sizes and process sizes to allocate memory efficiently.",
     *     tags={"Memory Allocation"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *
     *             @OA\Schema(
     *                 type="object",
     *                 required={"Block", "Process"},
     *
     *                 @OA\Property(
     *                     property="Algorithm",
     *                     type="string",
     *                     description="Type of algorithm (e.g., worstfit)",
     *                     example="worstfit"
     *                 ),
     *                 @OA\Property(
     *                     property="Block",
     *                     type="string",
     *                     description="Sizes of memory blocks (space-separated)",
     *                     example="100 500 200 300 600"
     *                 ),
     *                 @OA\Property(
     *                     property="Process",
     *                     type="string",
     *                     description="Sizes of processes (space-separated)",
     *                     example="212 417 112 426"
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation with memory allocation results"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request due to incorrect input"
     *     )
     * )
     */
    public function worst_fit(Request $request)
    {
        if ($request->input('Algorithm') == 'worstfit') {
            $block_array = explode(' ', $request->get('Block'));
            $process_array = explode(' ', $request->get('Process'));

            $j = 0;
            $processes = [];
            $is_process_complete = array_fill(0, count($process_array), false);
            $chart = [];

            while ($j < count($block_array)) {
                $shortest_process = 0;
                $process_num = -1;

                for ($i = 0; $i < count($process_array); $i++) {
                    if ($block_array[$j] <= $process_array[$i] && ! $is_process_complete[$i] && $process_array[$i] >= $shortest_process) {
                        $shortest_process = $process_array[$i];
                        $process_num = $i;
                    }
                }

                if ($process_num != -1) {
                    $processes[] = [
                        'process' => $j + 1,
                        'process_size' => $process_array[$process_num],
                        'block_size' => $block_array[$j],
                        'left_over' => $process_array[$process_num] -= $block_array[$j],
                    ];
                    $chart[] = [
                        'process' => $j + 1,
                        'chart' => $process_array,
                    ];
                    $is_process_complete[$process_num] = true;
                }
                $j++;
            }

            return response()->json(
                [
                    'process' => $processes,
                    'chart' => $chart,
                ],
            );
        }
    }

    /**
     * @OA\Post(
     *     path="/api/nextfit",
     *     summary="Simulate Next Fit Memory Allocation Algorithm",
     *     description="This API simulates the Next Fit memory allocation algorithm by accepting block sizes and process sizes to allocate memory efficiently.",
     *     tags={"Memory Allocation"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *
     *             @OA\Schema(
     *                 type="object",
     *                 required={"Block", "Process"},
     *
     *                 @OA\Property(
     *                     property="Algorithm",
     *                     type="string",
     *                     description="Type of algorithm (e.g., nextfit)",
     *                     example="nextfit"
     *                 ),
     *                 @OA\Property(
     *                     property="Block",
     *                     type="string",
     *                     description="Sizes of memory blocks (space-separated)",
     *                     example="100 500 200 300 600"
     *                 ),
     *                 @OA\Property(
     *                     property="Process",
     *                     type="string",
     *                     description="Sizes of processes (space-separated)",
     *                     example="212 417 112 426"
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation with memory allocation results"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request due to incorrect input"
     *     )
     * )
     */
    public function next_fit(Request $request)
    {
        if ($request->input('Algorithm') == 'nextfit') {
            $block_array = explode(' ', $request->get('Block'));
            $process_array = explode(' ', $request->get('Process'));

            $j = 0;
            $processes = [];
            $chart = [];
            $is_process_complete = array_fill(0, count($process_array), false);
            $process_num = 0;

            while ($j < count($block_array)) {
                $is_process = false;

                for ($i = $process_num; $i < count($process_array); $i++) {
                    if ($block_array[$j] <= $process_array[$i] && ! $is_process_complete[$i]) {
                        $is_process = true;
                        $process_num = $i;
                        break;
                    }
                }

                if ($is_process) {
                    $is_process_complete[$process_num] = true;
                    $processes[] = [
                        'process' => $j + 1,
                        'process_size' => $process_array[$process_num],
                        'block_size' => $block_array[$j],
                        'left_over' => $process_array[$process_num] -= $block_array[$j],
                    ];
                    $chart[] = [
                        'process' => $j + 1,
                        'chart' => $process_array,
                    ];
                }

                if ($process_num == (count($process_array) - 1)) {
                    $process_num = 0;
                }

                $j++;
            }

            return response()->json(
                [
                    'process' => $processes,
                    'chart' => $chart,
                ],
            );
        }
    }
}
