<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function insertUsersFromFile()
    {
        $repeated_tels = [];
        $inserted_tels = [];
        $getFile = Storage::disk('public')->get('users.txt');
        $lines = preg_split('/\r\n|\n|\r/', trim($getFile));
        foreach ($lines as $line) {
            $parts = preg_split('/\s+/', $line);

            $national_code = $parts[0];
            $tel           = $parts[1];

            if (!in_array($tel, $inserted_tels)) {
                 User::create([
                     'tel'           => $tel,
                     'national_code' => $national_code
                 ]);
                array_push($inserted_tels, $tel);
            }
            else {
                $repeated_tels[] = [
                    $national_code,
                    $tel
                ];
            }
        }

        if (!empty($repeated_tels)) {
            foreach ($repeated_tels as $row) {
                $text = $row[0].'  '.$row[1];
                Storage::put( '/public/repeated-users.txt', $text);
            }
        }
    }
}
