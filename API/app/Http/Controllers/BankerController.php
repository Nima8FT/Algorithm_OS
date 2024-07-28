<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BankerController extends Controller
{
    public function banker(Request $request)
    {
        if ($request->input("Algorithm") == "banker") {

            $allocation = explode('#', $request->get('Allocation'));
            $allocation_matrices = [];
            foreach ($allocation as $key => $value) {
                $allocation_matrices["P" . $key + 1] = explode(',', $value);
            }


            $max = explode('#', $request->get('Max'));
            $max_matrices = [];
            foreach ($max as $key => $value) {
                $max_matrices["P" . $key + 1] = explode(',', $value);
            }

            $instance = explode(' ', $request->get('Instance'));


            $allocation_array = array_fill(0, count($instance), 0);
            for ($i = 0; $i < count($allocation); $i++) {
                for ($j = 0; $j < count($instance); $j++) {
                    $allocation_array[$j] += (int) $allocation_matrices["P" . $i + 1][$j];
                }
            }

            $available = [];
            for ($i = 0; $i < count($instance); $i++) {
                $available[$i] = $instance[$i] - $allocation_array[$i];
            }

            $need = [];
            for ($i = 0; $i < count($max); $i++) {
                for ($j = 0; $j < count($instance); $j++) {
                    $need["P" . $i + 1][$j] = $max_matrices["P" . $i + 1][$j] - $allocation_matrices["P" . $i + 1][$j];
                }
            }

            $available_array["Resource"] = $available;
            $index = -1;
            $k = 0;
            $is_process_complete = array_fill(0, count($need), false);

            while ($k < count($need)) {
                for ($j = 0; $j < count($instance); $j++) {
                    if ($need["P" . $k + 1][$j] > $available[$j]) {
                        $index = -1;
                        break;
                    }
                    $index = $k;
                }

                if ($index !== -1 && !$is_process_complete[$index]) {
                    for ($j = 0; $j < count($instance); $j++) {
                        $available[$j] += $allocation_matrices["P" . $index + 1][$j];
                        $available_array["P" . $index + 1] = $available;
                    }
                    $is_process_complete[$index] = true;
                }


                if ($k == count($need) - 1) {
                    for ($i = 0; $i < count($need); $i++) {
                        if (!$is_process_complete[$i]) {
                            $k = -1;
                            break;
                        }
                    }
                }

                $k++;
            }

            $process_sequence = [];
            foreach ($available_array as $key => $value) {
                array_push($process_sequence, $key);
            }
            unset($process_sequence[0]);
            $process_sequence = array_values($process_sequence);

            return response()->json(
                [
                    "Instance" => $instance,
                    "Allocation" => $allocation_matrices,
                    "Max" => $max_matrices,
                    "Need" => $need,
                    "Available" => $available_array,
                    "Process Sequence" => $process_sequence,
                ],
            );
        }
    }
}
