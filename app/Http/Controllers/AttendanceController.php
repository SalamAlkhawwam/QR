<?php

namespace App\Http\Controllers;

use App\Models\NormalUser;
use App\Models\Session;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;


class AttendanceController extends Controller
{

    public function showSessionQr(Session $session)
    {
        $dateNow = Carbon::now()->toDateString();
        $timeNow = Carbon::now();

        $startSession = Carbon::parse($session['startSession']);
        $closeSession = Carbon::parse($session['closeSession']);

        if($session['date'] == $dateNow && $timeNow->between($startSession, $closeSession)) {

            $password = Str::random(12);

            $qrCodeData = QrCode::format('png')->generate("{$session['id']}|{$password}|{$dateNow}|{$timeNow}");

            $base64Image = base64_encode($qrCodeData);

            $session->update(['password' => $password]);

            return Response::make($base64Image, 200)->header('Content-Type', 'image/png');
        }
        else
            return response('Session is not active or time is incorrect.', 403);
    }

    public function scanQr(Request $request)
    {
        $sessionId = $request->input('sessionId');
        $password = $request->input('password');
        $dateNow = $request->input('dateNow');
        $timeNow = Carbon::parse($request->input('timeNow'));

        $session = Session::find($sessionId);

        if($password == $session->password
            && Carbon::now()->toDateString() == $dateNow
            && Carbon::now()->between($timeNow->addMinutes(1), $timeNow))
        {
            $normalUser = NormalUser::where('userID', Auth::id())->first();

            Attendance::create([
                'normalUserID' => $normalUser->id,
                'sessionID' => $sessionId,
            ]);
            return response()->json(['message' => 'Attendance recorded successfully.']);
        }
        return response()->json(['message' => 'Attendance is not recorded successfully.']);
    }

    public function register(Request $request)
    {
        $user = User::create([
            'name' => $request['name'],
            'password' => $request['password']
        ]);

        NormalUser::create([
            'userID' => $user->id,
        ]);

        $token = $user->createToken('MyApp')->accessToken;

        return response()->json($token, \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function login(Request $request)
    {
        $credentials = request(['email','password']);

        if(!Auth::attempt($credentials)){
            throw new AuthenticationException();
        }
        $data= $this->createToken($request->user());

        if(request()->is('api/*')){
            return response()->json($data,Response::HTTP_OK);
        }
        return view('Common.ChangePasswordPageForChangePassword');
    }

}
