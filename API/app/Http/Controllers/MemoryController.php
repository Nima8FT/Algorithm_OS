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
}
