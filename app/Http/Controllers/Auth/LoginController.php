<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | Controller ini menangani proses autentikasi pengguna dan mengarahkan
    | mereka ke halaman admin setelah berhasil login.
    |
    */

    use AuthenticatesUsers;

    /**
     * Ke mana pengguna diarahkan setelah login.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    /**
     * Membuat instance controller baru.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Menampilkan form login kustom.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Menangani proses login.
     * Jika gagal, kirim pesan error kustom ke halaman login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Validasi sederhana (jangan ubah logika utamanya)
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        // Coba login
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended($this->redirectPath());
        }

        // Jika gagal, kembalikan ke login dengan error seperti di desain
        return back()
            ->withInput($request->only('email'))
            ->with('error', 'Username & Password are incorrect');
    }

    /**
     * Mengembalikan path redirect setelah login berhasil.
     *
     * @return string
     */
    public function redirectPath(): string
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

        return property_exists($this, 'redirectTo')
            ? $this->redirectTo
            : '/admin';
    }
}
