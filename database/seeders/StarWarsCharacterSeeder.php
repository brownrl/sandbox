<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StarWarsCharacter;

class StarWarsCharacterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $characters = [
            [
                'name' => 'Luke Skywalker',
                'slug' => 'luke-skywalker',
                'description' => 'The hopeful Jedi Knight'
            ],
            [
                'name' => 'Han Solo',
                'slug' => 'han-solo',
                'description' => 'The charming smuggler'
            ],
            [
                'name' => 'Leia Organa',
                'slug' => 'leia-organa',
                'description' => 'The fearless leader'
            ],
            [
                'name' => 'Chewbacca',
                'slug' => 'chewbacca',
                'description' => 'The loyal Wookiee'
            ],
            [
                'name' => 'R2-D2',
                'slug' => 'r2-d2',
                'description' => 'The brave astromech droid'
            ],
            [
                'name' => 'C-3PO',
                'slug' => 'c-3po',
                'description' => 'The protocol droid'
            ],
            [
                'name' => 'Darth Vader',
                'slug' => 'darth-vader',
                'description' => 'The conflicted Sith Lord'
            ],
            [
                'name' => 'Yoda',
                'slug' => 'yoda',
                'description' => 'The wise Jedi Master'
            ],
            [
                'name' => 'Obi-Wan Kenobi',
                'slug' => 'obi-wan-kenobi',
                'description' => 'The noble Jedi'
            ],
            [
                'name' => 'Rey',
                'slug' => 'rey',
                'description' => 'The determined scavenger turned Jedi'
            ],
            [
                'name' => 'Lando Calrissian',
                'slug' => 'lando-calrissian',
                'description' => 'The smooth-talking gambler and administrator'
            ],
            [
                'name' => 'Padmé Amidala',
                'slug' => 'padme-amidala',
                'description' => 'The diplomatic Queen and Senator'
            ],
            [
                'name' => 'Mace Windu',
                'slug' => 'mace-windu',
                'description' => 'The skilled Jedi Master with a purple lightsaber'
            ],
            [
                'name' => 'Qui-Gon Jinn',
                'slug' => 'qui-gon-jinn',
                'description' => 'The wise and unconventional Jedi Master'
            ],
            [
                'name' => 'Anakin Skywalker',
                'slug' => 'anakin-skywalker',
                'description' => 'The chosen one turned Darth Vader'
            ],
            [
                'name' => 'Emperor Palpatine',
                'slug' => 'emperor-palpatine',
                'description' => 'The manipulative Sith Lord and Emperor'
            ],
            [
                'name' => 'Boba Fett',
                'slug' => 'boba-fett',
                'description' => 'The notorious bounty hunter'
            ],
            [
                'name' => 'Jabba the Hutt',
                'slug' => 'jabba-the-hutt',
                'description' => 'The crime lord with a taste for entertainment'
            ],
            [
                'name' => 'Kylo Ren',
                'slug' => 'kylo-ren',
                'description' => 'The conflicted villain with a temper'
            ],
            [
                'name' => 'Finn',
                'slug' => 'finn',
                'description' => 'The former stormtrooper turned Resistance fighter'
            ]
        ];

        foreach ($characters as $character) {
            StarWarsCharacter::updateOrCreate(
                ['slug' => $character['slug']],
                $character
            );
        }
    }
}
