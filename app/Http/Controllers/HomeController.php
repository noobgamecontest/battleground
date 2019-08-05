<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Message\MessageService;

class HomeController extends Controller
{
    /**
     * @var MessageService
     */
    protected $messageService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;

        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     * @throws \App\Services\Message\UnexpectedMessageTypeException
     */
    public function index()
    {
        $this->messageService->set('info', 'Bienvenue !');

        return view('home');
    }
}
