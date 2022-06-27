<?php

namespace Pterodactyl\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Prologue\Alerts\AlertsMessageBag;
use Pterodactyl\Http\Controllers\Controller;

class AnnouncementsController extends Controller
{
    /**
     * @var \Prologue\Alerts\AlertsMessageBag
     */
    protected $alert;

    /**
     * AnnouncementsController constructor.
     * @param AlertsMessageBag $alert
     */
    public function __construct(AlertsMessageBag $alert)
    {
        $this->alert = $alert;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $announcements = DB::table('announcements')->orderBy('updated_at', 'DESC')->get();

        return view('admin.announcements.list', [
            'announcements' => $announcements
        ]);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin.announcements.new');
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        $id = (int) $id;

        $announcement = DB::table('announcements')->where('id', '=', $id)->get();
        if (count($announcement) < 1) {
            $this->alert->danger('Announcement not found!')->flash();
            return redirect()->route('admin.announcements');
        }

        return view('admin.announcements.edit', [
            'announcement' => $announcement[0]
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function new(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:100',
            'body' => 'required'
        ]);

        $title = trim(strip_tags($request->input('title')));
        $body = trim($request->input('body'));

        DB::table('announcements')->insert([
            'title' => $title,
            'body' => $body,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);

        $this->alert->success('You have successfully created this announcement.')->flash();

        return redirect()->route('admin.announcements');
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id)
    {
        $id = (int) $id;

        $this->validate($request, [
            'title' => 'required|max:100',
            'body' => 'required'
        ]);

        $title = trim(strip_tags($request->input('title')));
        $body = trim($request->input('body'));

        $isset = DB::table('announcements')->where('id', '=', $id)->get();
        if (count($isset) < 1) {
            $this->alert->danger('Announcement not found!')->flash();
            return redirect()->route('admin.announcements.edit', $id);
        }

        DB::table('announcements')->where('id', '=', $id)->update([
            'title' => $title,
            'body' => $body,
            'updated_at' => \Carbon\Carbon::now()
        ]);

        $this->alert->success('You have successfully edited this announcement.')->flash();

        return redirect()->route('admin.announcements');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request): JsonResponse
    {
        $id = (int) $request->input('id');

        $isset = DB::table('announcements')->where('id', '=', $id)->get();
        if (count($isset) < 1) {
            return response()->json(['success' => false, 'error' => 'Announcement not found!']);
        }

        DB::table('announcements')->where('id', '=', $id)->delete();

        return response()->json(['success' => true]);
    }
}
