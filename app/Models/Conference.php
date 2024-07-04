<?php

namespace App\Models;

use App\Enums\Status;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Enums\Region;

class Conference extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'status',
        'region',
        'venue_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'region' => Region::class,
        'venue_id' => 'integer',
    ];

    public static function getForm()
    {
        return[
                Tabs::make()
                    ->columns(2)
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Conference Details')
                            ->columnSpanFull()
                            ->schema([
                                TextInput::make('name')
                                    ->label('Company Name')
                                    ->default('Name')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('status')
                                    ->label('Status')
                                    ->options(Status::class),
                                MarkdownEditor::make('description')
                                    ->columnSpanFull()
                                    ->required()
                                    ->maxLength(255),
                                DatePicker::make('start_date')
                                    ->required(),
                                DatePicker::make('end_date')
                                    ->required(),

                            ]),

                        Tabs\Tab::make('Location')
                            ->columnSpanFull()
                            ->schema([
                                Select::make('region')
                                    ->live()
                                    ->enum(Region::class)
                                    ->options(Region::class),
                                Select::make('venue_id')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm(Venue::getForm())
                                    ->editOptionForm(Venue::getForm())
                                    ->editOptionModalHeading('Edit Venue')
                                    ->relationship('venue', 'name', modifyQueryUsing: function (Builder $query, Forms\Get $get){
                                        return $query->where('region',$get('region'));
                                    }),
                                CheckboxList::make('speakers')
                                    ->relationship('speakers','name')
                                    ->options(
                                        Speaker::all()->pluck('name','id')
                                    )->columns(2)
                                    ->searchable()
                                    ->required(),
                            ]),

                    ]),
                        Actions::make([
                            Action::make('star')
                                ->icon('heroicon-m-star')
                                ->visible( function (string $operation){
                                    if ($operation !== 'create'){
                                        return false;
                                    }

                                    if(!app()->environment('local')){
                                        return false;
                                    }

                                    return true;
                                })
                                ->action(function ($livewire) {
                                    $data = Conference::factory()->make()->toArray();
                                    $livewire->form->fill($data);
                                }),

                        ]),


        ];
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function speakers(): BelongsToMany
    {
        return $this->belongsToMany(Speaker::class);
    }

    public function talks(): BelongsToMany
    {
        return $this->belongsToMany(Talk::class);
    }
}
