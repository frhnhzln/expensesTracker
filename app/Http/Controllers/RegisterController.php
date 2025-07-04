<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use Illuminate\Support\Facades\Session;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function register()
    {
        return view('register');
    }

    public function validate_qr()
    {
        $users = \App\Models\Profile::whereNotNull('qr_code')->get();

        return view('validate_qr', compact('users'));
    }

    public function register_process(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:profile,username',
            'password' => 'required|min:6|confirmed',
            'face_id' => 'required',
        ]);

        if (strcasecmp($request->captcha, Session::get('captcha')) !== 0) {
            return response()->json([
                'errors' => ['captcha' => ['Incorrect CAPTCHA. Try again.']]
            ], 422);
        }

        $faceId = json_decode($request->face_id);
        if (!is_array($faceId) || count($faceId) !== 128) {
            return response()->json([
                'errors' => ['face_id' => ['Invalid face scan data. Please try again.']]
            ], 422);
        }

        $qrContent = "Username: {$request->username}\nPassword: {$request->password}";
        $qrTextBase64 = base64_encode($qrContent);
        $qrSvg = \QrCode::format('svg')->size(200)->generate($qrContent);

        \App\Models\Profile::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'qr_code' => $qrTextBase64,
            'face_id' => $request->face_id,
        ]);

        return response()->json([
            'message' => 'Registration successful!',
            'qr' => 'data:image/svg+xml;base64,' . base64_encode($qrSvg),
        ]);
    }

    public function qr_login(Request $request)
    {
        $decodedText = $request->input('qr_content');

        $users = \App\Models\Profile::whereNotNull('qr_code')->get();

        foreach ($users as $user) {
            $decodedQr = base64_decode($user->qr_code);
            if (strpos($decodedQr, $decodedText) !== false) {
                // Log in the user
                Auth::login($user);

                return response()->json([
                    'success' => true,
                    'username' => $user->username
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid QR Code.'
        ], 401);
    }

    public function showFaceLogin()
    {
        return view('validateFace');
    }

   public function processFaceLogin(Request $request)
    {
        $inputEncoding = json_decode($request->input('face_id'));

        if (!is_array($inputEncoding) || count($inputEncoding) !== 128) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid face data.'
            ], 422);
        }

        $users = Profile::whereNotNull('face_id')->get();

        foreach ($users as $user) {
            $storedEncoding = json_decode($user->face_id);
            if (!is_array($storedEncoding) || count($storedEncoding) !== 128) {
                continue;
            }

            // Calculate Euclidean distance
            $distance = 0;
            for ($i = 0; $i < 128; $i++) {
                $diff = $inputEncoding[$i] - $storedEncoding[$i];
                $distance += $diff * $diff;
            }
            $distance = sqrt($distance);

            if ($distance < 0.3) {
                Auth::login($user);

                return response()->json([
                    'success' => true,
                    'message' => 'Face recognized.',
                    'username' => $user->username
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Face not recognized.'
        ], 401);
    }

    public function forgot_pwd()
    {
        return view('forgot_pwd');
    }

    //captcha
    public function generate()
    {
        if (session_status() === PHP_SESSION_NONE) {
        session_start(); // ensure session is active
        }

        $code = substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"), 0, 5);
        Session::put('captcha', $code);

        $image = imagecreatetruecolor(120, 40);
        $bgColor = imagecolorallocate($image, 255, 255, 255);
        $textColor = imagecolorallocate($image, 0, 0, 0);
        imagefilledrectangle($image, 0, 0, 120, 40, $bgColor);
        imagestring($image, 5, 30, 10, $code, $textColor);

        ob_start();
        imagepng($image);
        $imgData = ob_get_clean();

        return response()->make($imgData, 200, ['Content-Type' => 'image/png']);
    }

    //face-id
    public function storeFace(Request $request)
    {
        $userId = auth()->id();

        $encoding = $request->input('encoding');

        if (!$encoding) {
            return response()->json(['status' => 'error', 'message' => 'Face encoding is missing.'], 400);
        }

        DB::table('face_encodings')->updateOrInsert(
            ['user_id' => $userId],
            ['face_encoding' => $encoding]
        );

        return response()->json(['status' => 'success']);
    }
}
