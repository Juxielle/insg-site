<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Article;
use App\Models\Event;
use App\Models\Partner;
use App\Models\Page;
use App\Models\Program;
use App\Models\SiteStatistic;
use App\Models\Testimonial;
use App\Models\Contest;
use Illuminate\Contracts\View\View;

class SiteController extends Controller
{
    public function home(): View
    {
        return view('home', [
            'page' => $this->page('home'),
            'statistics' => SiteStatistic::orderBy('sort_order')->get(),
            'programs' => Program::where('active', true)->where('featured', true)->orderBy('sort_order')->get(),
            'articles' => Article::whereNotNull('published_at')->latest('published_at')->limit(3)->get(),
            'events' => Event::where('starts_at', '>=', now()->startOfDay())->orderBy('starts_at')->limit(3)->get(),
            'testimonials' => Testimonial::where('featured', true)->get(),
            'partners' => Partner::where('active', true)->orderBy('id')->limit(6)->get(),
            'publishedContests' => Contest::where('status', 'results_published')->latest('published_at')->limit(3)->get(),
        ]);
    }

    public function programs(): View
    {
        return view('pages.cms-page', ['page' => $this->page('formations'), 'programs' => Program::where('active', true)->orderBy('sort_order')->get()]);
    }

    public function articles(): View
    {
        return view('pages.cms-page', ['page' => $this->page('actualites'), 'articles' => Article::whereNotNull('published_at')->latest('published_at')->get()]);
    }

    public function announcements(): View
    {
        return view('pages.cms-page', ['page' => $this->page('annonces-concours'), 'announcements' => Announcement::latest('published_at')->get()]);
    }

    public function studentLife(): View
    {
        return view('pages.cms-page', ['page' => $this->page('vie-etudiante'), 'events' => Event::orderBy('starts_at')->get()]);
    }

    public function partners(): View
    {
        return view('pages.cms-page', ['page' => $this->page('entreprises'), 'partners' => Partner::where('active', true)->get()]);
    }

    public function staticPage(string $page): View
    {
        $allowed = ['about', 'admissions', 'bibliotheque', 'contact', 'incubateur', 'inscription-master', 'recherche'];
        abort_unless(in_array($page, $allowed, true), 404);

        return view('pages.cms-page', [
            'page' => $this->page($page),
            'programs' => $page === 'admissions' ? Program::where('active', true)->orderBy('sort_order')->get() : collect(),
        ]);
    }

    private function page(string $slug): Page
    {
        return Page::with(['sections' => fn ($query) => $query->where('active', true)->orderBy('sort_order')])
            ->where('slug', $slug)->where('active', true)->firstOrFail();
    }
}
