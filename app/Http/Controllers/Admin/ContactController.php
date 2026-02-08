<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display a listing of contact submissions.
     */
    public function index(Request $request)
    {
        $query = ContactSubmission::query();

        // Filter by read/unread status
        if ($request->has('filter')) {
            $filter = $request->get('filter');
            if ($filter === 'unread') {
                $query->unread();
            } elseif ($filter === 'read') {
                $query->where('is_read', true);
            }
        }

        $contacts = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.contacts.index', compact('contacts'));
    }

    /**
     * Display the specified contact submission.
     */
    public function show(ContactSubmission $contact)
    {
        // Mark as read when viewing
        if (!$contact->is_read) {
            $contact->update(['is_read' => true]);
        }

        return view('admin.contacts.show', compact('contact'));
    }

    /**
     * Toggle the is_read status of a contact submission.
     */
    public function markAsRead(ContactSubmission $contact)
    {
        $contact->update(['is_read' => !$contact->is_read]);

        return redirect()
            ->back()
            ->with('success', $contact->is_read ? 'Contact marked as read.' : 'Contact marked as unread.');
    }

    /**
     * Remove the specified contact submission from storage.
     */
    public function destroy(ContactSubmission $contact)
    {
        $contact->delete();

        return redirect()
            ->route('admin.contacts.index')
            ->with('success', 'Contact submission deleted successfully.');
    }
}
