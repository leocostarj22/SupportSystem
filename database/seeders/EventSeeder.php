<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            [
                'title' => 'Reunião de Planejamento',
                'description' => 'Discussão sobre novos projetos',
                'start' => Carbon::now()->addDays(2)->setHour(10),
                'end' => Carbon::now()->addDays(2)->setHour(12),
                'color' => '#4CAF50',
                'all_day' => false,
                'user_id' => 1,
            ],
            [
                'title' => 'Treinamento da Equipe',
                'description' => 'Capacitação em novas tecnologias',
                'start' => Carbon::now()->addDays(5),
                'end' => Carbon::now()->addDays(5)->addHours(8),
                'color' => '#2196F3',
                'all_day' => true,
                'user_id' => 1,
            ],
            [
                'title' => 'Manutenção do Sistema',
                'description' => 'Atualização de rotina',
                'start' => Carbon::now()->addDays(-1)->setHour(15),
                'end' => Carbon::now()->addDays(-1)->setHour(17),
                'color' => '#FFC107',
                'all_day' => false,
                'user_id' => 1,
            ],
            [
                'title' => 'Apresentação do Projeto',
                'description' => 'Demonstração para o cliente',
                'start' => Carbon::now()->addDays(7)->setHour(14),
                'end' => Carbon::now()->addDays(7)->setHour(16),
                'color' => '#9C27B0',
                'all_day' => false,
                'user_id' => 1,
            ],
        ];

        foreach ($events as $event) {
            Event::create($event);
        }
    }
}