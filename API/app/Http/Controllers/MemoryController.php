<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MemoryController extends Controller
{
    public function best_fit(Request $request)
    {
        if ($request->input('Algorithm') == "BestFit") {
            $block_array = explode(' ', $request->get("Block"));
            $process_array = explode(' ', $request->get("Process"));

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

    public function first_fit(Request $request)
    {
        if ($request->input('Algorithm') == "FirstFit") {
            $block_array = explode(' ', $request->get("Block"));
            $process_array = explode(' ', $request->get("Process"));

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

    public function worst_fit(Request $request)
    {
        if ($request->input('Algorithm') == "WorstFit") {
            $block_array = explode(' ', $request->get("Block"));
            $process_array = explode(' ', $request->get("Process"));

            $j = 0;
            $processes = [];
            $is_process_complete = array_fill(0, count($process_array), false);
            $chart = [];

            while ($j < count($block_array)) {
                $shortest_process = 0;
                $process_num = -1;

                for ($i = 0; $i < count($process_array); $i++) {
                    if ($block_array[$j] <= $process_array[$i] && !$is_process_complete[$i] && $process_array[$i] >= $shortest_process) {
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

    public function next_fit(Request $request)
    {
        if ($request->input('Algorithm') == "NextFit") {
            $block_array = explode(' ', $request->get("Block"));
            $process_array = explode(' ', $request->get("Process"));

            $j = 0;
            $processes = [];
            $chart = [];
            $is_process_complete = array_fill(0, count($process_array), false);
            $process_num = 0;

            while ($j < count($block_array)) {
                $is_process = false;

                for ($i = $process_num; $i < count($process_array); $i++) {
                    if ($block_array[$j] <= $process_array[$i] && !$is_process_complete[$i]) {
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
