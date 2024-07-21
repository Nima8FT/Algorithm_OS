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

    public function lru(Request $request)
    {
        if ($request->input('Algorithm') == "LRU") {
            $frames = $request->get("Frames");
            $refrences = explode(' ', $request->get("Refrences"));

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
                                'page fault' => 'miss',
                            ];
                            break;
                        }
                    }

                    if ($is_process) {
                        for ($i = $j - 1; $i >= 0; $i--) {
                            switch ($refrences[$i]) {
                                case $frame[0]:
                                    if (!$is_frame[0]) {
                                        $count++;
                                        $id_frame = 0;
                                        $is_frame[0] = true;
                                        break;
                                    }

                                case $frame[1]:
                                    if (!$is_frame[1]) {
                                        $count++;
                                        $id_frame = 1;
                                        $is_frame[1] = true;
                                        break;
                                    }

                                case $frame[2]:
                                    if (!$is_frame[2]) {
                                        $count++;
                                        $id_frame = 2;
                                        $is_frame[2] = true;
                                        break;
                                    }
                            }

                            if ($count == 3) {
                                unset($frame[$id_frame]);
                                array_push($frame, $refrences[$j]);
                                $frame = array_values($frame);
                                $page_fault++;
                                $chart[] = [
                                    'process' => $refrences[$j],
                                    'frame' => $frame,
                                    'page fault' => 'hit',
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