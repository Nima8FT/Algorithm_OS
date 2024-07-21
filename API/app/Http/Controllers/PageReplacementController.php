<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageReplacementController extends Controller
{
    public function fifo(Request $request)
    {
        if ($request->input('Algorithm') == "FIFO") {
            $frames = $request->get("Frames");
            $refrences = explode(' ', $request->get("Refrences"));

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
                                'page fault' => 'miss',
                            ];
                            break;
                        }
                    }

                    if ($is_process == 1) {
                        array_push($frame, $refrences[$j]);
                        unset($frame[0]);
                        $frame = array_values($frame);
                        $page_fault++;
                        $chart[] = [
                            'process' => $refrences[$j],
                            'frame' => $frame,
                            'page fault' => 'hit',
                        ];
                    }
                } else {
                    array_push($frame, $refrences[$j]);
                    $page_fault++;
                    $chart[] = [
                        'process' => $refrences[$j],
                        'frame' => $frame,
                        'page fault' => 'hit',
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
