<?php

namespace App\Models;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\Region;

class Venue extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'city',
        'country',
        'postal_code',
        'region',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'region' => Region::class
    ];

    public static function getForm(): array
    {
        return[
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('city')
                    ->required()
                    ->maxLength(255),
                TextInput::make('country')
                    ->required()
                    ->maxLength(255),
                Select::make('region')
                    ->enum(Region::class)
                    ->options(Region::class),
                TextInput::make('postal_code')
                    ->required()
                    ->maxLength(255),
            ];
    }

    public function conferences(): HasMany
    {
        return $this->hasMany(Conference::class);
    }
}
