<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatbotTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_chatbot_answers_with_dynamic_program_information(): void
    {
        $this->getJson(route('chatbot.answer', ['message' => 'Quelles formations proposez-vous ?']))
            ->assertOk()
            ->assertJsonStructure(['answer', 'links', 'suggestions'])
            ->assertJsonPath('links.0.label', 'Découvrir les formations');
    }

    public function test_chatbot_validates_empty_and_oversized_questions(): void
    {
        $this->getJson(route('chatbot.answer'))->assertUnprocessable();
        $this->getJson(route('chatbot.answer', ['message' => str_repeat('a', 501)]))->assertUnprocessable();
    }
}
