<?php

namespace App\Http\Controllers;

use App\Http\Requests\EclContactRequest;
use App\Mail\EclContactMail;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class EclContactController extends Controller
{
    /**
     * Display the ECL contact form.
     */
    public function show()
    {
        return Inertia::render('EclContactDemo', [
            'title' => 'ECL Contact Form Demo',
        ])->rootView('ecl');
    }

    /**
     * Handle the contact form submission.
     */
    public function store(EclContactRequest $request)
    {
        $validated = $request->validated();

        // Send the email
        Mail::to(config('mail.from.address'))->send(
            new EclContactMail(
                $validated['name'],
                $validated['email'],
                $validated['subject'],
                $validated['message']
            )
        );

        // Return success response for Inertia
        return redirect()->route('ecl-contact-demo')->with([
            'message' => 'Thank you for your message! We\'ll get back to you soon.',
            'form_submitted' => true,
        ]);
    }
}
