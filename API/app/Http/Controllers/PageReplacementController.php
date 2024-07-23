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
                            if (count($frame) == $frames) {
                                for ($k = 0; $k < $frames; $k++) {
                                    if ($refrences[$i] == $frame[$k] && !$is_frame[$k]) {
                                        $count++;
                                        $id_frame = $k;
                                        $is_frame[$k] = true;
                                    }
                                }
                            }

                            if ($count == $frames) {
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

    public function mru(Request $request)
    {
        if ($request->input('Algorithm') == "MRU") {
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

                    if ($is_process) {
                        $page_fault++;
                        for ($i = 0; $i < $frames; $i++) {
                            if ($refrences[$j - 1] == $frame[$i]) {
                                unset($frame[$i]);
                            }
                        }
                        array_push($frame, $refrences[$j]);
                        $frame = array_values($frame);
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

    public function lifo(Request $request)
    {
        if ($request->input('Algorithm') == "LIFO") {
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

                    if ($is_process) {
                        $page_fault++;
                        for ($i = $j - 1; $i >= 0; $i--) {
                            if (count($frame) == $frames) {
                                for ($k = 0; $k < $frames; $k++) {
                                    if ($refrences[$i] == $frame[$k] && $chart[$i]["page fault"] == "hit") {
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

    public function lfu(Request $request)
    {
        if ($request->input('Algorithm') == "LFU") {
            $frames = $request->get("Frames");
            $refrences = explode(' ', $request->get("Refrences"));
            $refrences_unique = array_unique($refrences);
            $refrences_unique = array_values($refrences_unique);
            $refrences_frequence = array_fill(0, count($refrences_unique), 0);
            $refrences_unique_array = array_combine($refrences_unique, $refrences_frequence);

            $j = 0;
            $frame = [];
            $page_fault = 0;
            $chart = [];

            while ($j < count($refrences)) {
                $current = $refrences[$j];

                if (in_array($current, $frame)) {
                    $refrences_unique_array[$current]++;
                    $chart[] = [
                        'process' => $current,
                        'frame' => $frame,
                        'page fault' => 'miss',
                    ];
                } else {
                    if (count($frame) < $frames) {
                        $frame[] = $current;
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
                        $refrences_unique_array[$frame[$key]]--;
                        unset($frame[$key]);
                        $frame = array_values($frame);
                        $frame[] = $current;
                    }

                    $refrences_unique_array[$current]++;
                    $page_fault++;
                    $chart[] = [
                        'process' => $current,
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

    public function mfu(Request $request)
    {
        if ($request->input('Algorithm') == "MFU") {
            $frames = $request->get("Frames");
            $refrences = explode(' ', $request->get("Refrences"));
            $refrences_unique = array_unique($refrences);
            $refrences_unique = array_values($refrences_unique);
            $refrences_frequence = array_fill(0, count($refrences_unique), 0);
            $refrences_unique_array = array_combine($refrences_unique, $refrences_frequence);

            $j = 0;
            $frame = [];
            $page_fault = 0;
            $chart = [];

            while ($j < count($refrences)) {
                $current = $refrences[$j];

                if (in_array($current, $frame)) {
                    $refrences_unique_array[$current]++;
                    $chart[] = [
                        'process' => $current,
                        'frame' => $frame,
                        'page fault' => 'miss',
                    ];
                } else {
                    if (count($frame) < $frames) {
                        $frame[] = $current;
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
                        $refrences_unique_array[$frame[$key]] = 0;
                        unset($frame[$key]);
                        $frame = array_values($frame);
                        $frame[] = $current;
                    }

                    $refrences_unique_array[$current]++;
                    $page_fault++;
                    $chart[] = [
                        'process' => $current,
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

    public function rendomPageReplacement(Request $request)
    {
        if ($request->input('Algorithm') == "Random Page Replacement") {
            $frames = $request->get("Frames");
            $refrences = explode(' ', $request->get("Refrences"));

            $j = 0;
            $frame = [];
            $chart = [];
            $page_fault = 0;

            while ($j < count($refrences)) {
                if (in_array($refrences[$j], $frame)) {
                    $chart[] = [
                        "process" => $refrences[$j],
                        "frame" => $frame,
                        "page fault" => "miss",
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
                        "process" => $refrences[$j],
                        "frame" => $frame,
                        "page fault" => "hit",
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
