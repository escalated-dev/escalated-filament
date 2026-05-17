<?php

use Escalated\Filament\Resources\SkillResource\Pages\CreateSkill;
use Escalated\Filament\Tests\User;
use Escalated\Laravel\Models\Department;
use Escalated\Laravel\Models\Skill;
use Escalated\Laravel\Models\Tag;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->authenticateUser();
});

it('persists routing fields and agent proficiencies when creating a skill', function () {
    $tag = Tag::factory()->create();
    $department = Department::factory()->create();
    $agent = User::create([
        'name' => 'Line Agent',
        'email' => 'line-agent@test.com',
        'password' => bcrypt('password'),
    ]);

    livewire(CreateSkill::class)
        ->fillForm([
            'name' => 'Networking',
            'description' => 'Covers VLANs and switching.',
            'routing_tag_ids' => [$tag->id],
            'routing_department_ids' => [$department->id],
            'agents' => [
                ['user_id' => $agent->id, 'proficiency' => 4],
            ],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $skill = Skill::query()->where('name', 'Networking')->first();

    expect($skill)->not->toBeNull();
    expect($skill->description)->toBe('Covers VLANs and switching.');
    expect($skill->routing_tag_ids)->toBe([(int) $tag->id]);
    expect($skill->routing_department_ids)->toBe([(int) $department->id]);

    $skill->load('agents');
    expect($skill->agents)->toHaveCount(1);
    expect((int) $skill->agents->first()->pivot->proficiency)->toBe(4);
});
