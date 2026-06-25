<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $tab = $request->query('tab', 'all');

        $notifiableType = get_class($user);

        $query = match ($tab) {
            'unread' => $user->unreadNotifications(),
            'read'   => $user->notifications()->whereNotNull('read_at'),
            'trash'  => Notification::onlyTrashed()
                ->where('notifiable_id', $user->getKey())
                ->where('notifiable_type', $notifiableType),
            default  => $user->notifications(),
        };

        $notifications = $query->latest()->paginate(20);

        $totalCount   = $user->notifications()->count();
        $unreadCount  = $user->unreadNotifications()->count();
        $readCount    = $totalCount - $unreadCount;
        $trashedCount = Notification::onlyTrashed()
            ->where('notifiable_id', $user->getKey())
            ->where('notifiable_type', $notifiableType)
            ->count();
        $readRate = $totalCount > 0 ? round(($readCount / $totalCount) * 100) : 0;

        return view('notifications.index', compact(
            'notifications', 'tab', 'totalCount', 'unreadCount', 'readCount', 'trashedCount', 'readRate'
        ));
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead(Request $request)
    {
        $user = auth()->user();
        if ($request->filled('ids')) {
            $user->notifications()
                ->whereIn('id', (array) $request->ids)
                ->whereNull('read_at')
                ->get()
                ->each->markAsRead();
        } else {
            $user->unreadNotifications->markAsRead();
        }
        return back()->with('success', 'Notifications marked as read.');
    }

    public function markAsUnread($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->update(['read_at' => null]);
        return back()->with('success', 'Notification marked as unread.');
    }

    public function trash($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->delete();
        return back()->with('success', 'Notification moved to trash.');
    }

    public function trashAll(Request $request)
    {
        $user = auth()->user();
        if ($request->filled('ids')) {
            $user->notifications()
                ->whereIn('id', (array) $request->ids)
                ->get()
                ->each->delete();
        } else {
            $user->notifications()->get()->each->delete();
        }
        return back()->with('success', 'Notifications moved to trash.');
    }

    public function restore($id)
    {
        $notification = Notification::onlyTrashed()
            ->where('notifiable_id', auth()->id())
            ->findOrFail($id);
        $notification->restore();
        return back()->with('success', 'Notification restored.');
    }

    public function destroy($id)
    {
        $notification = Notification::onlyTrashed()
            ->where('notifiable_id', auth()->id())
            ->findOrFail($id);
        $notification->forceDelete();
        return back()->with('success', 'Notification permanently deleted.');
    }

    public function emptyTrash()
    {
        Notification::onlyTrashed()
            ->where('notifiable_id', auth()->id())
            ->where('notifiable_type', get_class(auth()->user()))
            ->forceDelete();
        return back()->with('success', 'Trash emptied.');
    }
}
