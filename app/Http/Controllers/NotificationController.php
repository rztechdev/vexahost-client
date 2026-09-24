<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Delete all notifications for the authenticated user.
     */
    public function destroyAll(Request $request)
    {
        $count = $request->user()->notifications()->count();
        $request->user()->notifications()->delete();

        return back()->with('success', "Seluruh {$count} notifikasi berhasil dihapus.");
    }

    /**
     * Delete only read notifications for the authenticated user.
     */
    public function destroyRead(Request $request)
    {
        $count = $request->user()->readNotifications()->count();
        $request->user()->readNotifications()->delete();

        return back()->with('success', "{$count} notifikasi yang sudah dibaca berhasil dihapus.");
    }
}
