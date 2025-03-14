<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Filament\Resources\EventResource;
use Filament\Actions;
use Saade\FilamentFullCalendar\Actions\CreateAction;
use Saade\FilamentFullCalendar\Data\EventData;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Form;
use Saade\FilamentFullCalendar\Actions\EditAction;
use Saade\FilamentFullCalendar\Actions\DeleteAction;

class CalendarWidget extends FullCalendarWidget
{
    public Model | string | null $model = Event::class;

    public function createEvent(array $data): void
    {
        Event::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'start' => $data['start'],
            'end' => $data['end'],
            'color' => $data['color'],
            'all_day' => $data['all_day'] ?? false,
            'user_id' => auth()->id(),
        ]);
    }

    public function updateEvent(array $data): void
    {
        $event = Event::find($data['id']);
        if ($event) {
            $event->fill($data);
            $event->user_id = $event->user_id ?? auth()->id();
            $event->save();
        }
    }

    public function fetchEvents(array $fetchInfo): array
    {
        return Event::query()
            ->when(auth()->user()->role?->name !== 'Super Admin', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->where('start', '>=', $fetchInfo['start'])
            ->where('end', '<=', $fetchInfo['end'])
            ->get()
            ->map(
                fn (Event $event) => EventData::make()
                    ->id($event->id)
                    ->title($event->title)
                    ->start($event->start)
                    ->end($event->end)
                    ->allDay($event->all_day)
                    ->backgroundColor($event->color)
                    ->borderColor($event->color)
                    ->extendedProps([
                        'description' => $event->description
                    ])
            )
            ->toArray();
    }

    public function getFormSchema(): array
    {
        return [
            TextInput::make('title')
                ->label('Título')
                ->required()
                ->maxLength(255),
            Textarea::make('description')
                ->label('Descrição')
                ->maxLength(65535),
            DateTimePicker::make('start')
                ->label('Início')
                ->required(),
            DateTimePicker::make('end')
                ->label('Fim')
                ->required()
                ->after('start'),
            ColorPicker::make('color')
                ->label('Cor')
                ->required(),
            Toggle::make('all_day')
                ->label('Dia inteiro')
                ->default(false),
            Forms\Components\Hidden::make('user_id')
                ->default(auth()->id()),
        ];
    }


    
    protected function headerActions(): array
    {
        return [
            CreateAction::make()
                ->label('Criar Evento')
                ->mountUsing(
                    function (Forms\Form $form, array $arguments) {
                        $form->fill([
                            'start' => $arguments['start'] ?? null,
                            'end' => $arguments['end'] ?? null,
                            'user_id' => auth()->id(),
                        ]);
                    }
                )
        ];
    }

    protected function modalActions(): array
 {
     return [
         EditAction::make()
             ->mountUsing(
                 function (Event $record, Forms\Form $form, array $arguments) {
                     $form->fill([
                        'title' => $record->title,
                        'color' => $record->color,
                        'start' => $arguments['event']['start'] ?? $record->start,
                        'end' => $arguments['event']['end'] ?? $record->end,
                        'user_id' => auth()->id(),
                     ]);
                 }
             ),
         DeleteAction::make(),
     ];
 }

    protected function getOptions(): array
    {
        return [
            'locale' => 'pt-br',
            'initialView' => 'dayGridMonth',
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'dayGridMonth,timeGridWeek,timeGridDay',
            ],
            'editable' => true,
            'selectable' => true,
            'dayMaxEvents' => true,
            'eventDrop' => true,
            'eventResize' => true,
            'select' => true,
            'eventClick' => 'function(info) {
                info.jsEvent.preventDefault();
            }',
            'eventDidMount' => 'function(info) {
                if (typeof tippy !== "undefined") {
                    tippy(info.el, {
                        content: info.event.extendedProps.description || info.event.title,
                        placement: "top",
                        trigger: "mouseenter",
                        interactive: true
                    });
                }
            }',
            'eventMouseEnter' => 'function(info) {
                info.el.style.cursor = "pointer";
            }',
            'selectMirror' => true,
            'unselectAuto' => true,
            'selectOverlap' => false,
            'eventOverlap' => false,
        ];
    }

}
