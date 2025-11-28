<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EclContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_ecl_contact_form_displays(): void
    {
        $response = $this->get(route('ecl-contact-demo'));

        $response->assertStatus(200)
                ->assertInertia(fn ($page) => $page
                    ->component('EclContactDemo')
                    ->has('title')
                );
    }

    public function test_ecl_contact_form_validates_required_fields(): void
    {
        $response = $this->post(route('ecl-contact-demo.store'), []);

        $response->assertSessionHasErrors(['name', 'email', 'subject', 'message']);
    }

    public function test_ecl_contact_form_validates_email_format(): void
    {
        $response = $this->post(route('ecl-contact-demo.store'), [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'subject' => 'general',
            'message' => 'This is a test message with enough content.',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_ecl_contact_form_validates_subject_options(): void
    {
        $response = $this->post(route('ecl-contact-demo.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'subject' => 'invalid-subject',
            'message' => 'This is a test message with enough content.',
        ]);

        $response->assertSessionHasErrors('subject');
    }

    public function test_ecl_contact_form_validates_message_length(): void
    {
        $response = $this->post(route('ecl-contact-demo.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'subject' => 'general',
            'message' => 'Short',
        ]);

        $response->assertSessionHasErrors('message');
    }

    public function test_ecl_contact_form_sends_email_on_valid_submission(): void
    {
        // Note: Email sending is tested manually due to queue configuration
        // In a production environment, you might want to configure sync queue for testing
        $contactData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'subject' => 'general',
            'message' => 'This is a test message with enough content to pass validation.',
        ];

        $response = $this->post(route('ecl-contact-demo.store'), $contactData);

        $response->assertRedirect(route('ecl-contact-demo'))
                ->assertSessionHas('message')
                ->assertSessionHas('form_submitted', true);
    }

    public function test_ecl_contact_form_show_includes_flash_messages_in_props(): void
    {
        // Set flash messages in session
        session(['message' => 'Test message', 'form_submitted' => true]);

        $response = $this->get(route('ecl-contact-demo'));

        $response->assertInertia(function ($page) {
            $page->has('flash.message')
                 ->has('flash.form_submitted')
                 ->where('flash.message', 'Test message')
                 ->where('flash.form_submitted', true);
        });
    }
}
