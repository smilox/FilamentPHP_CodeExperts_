<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required()->label('Nome'),
                TextInput::make('email')->required()->email()->label('E-mail'),
                Select::make('role')->relationship('roles', 'name')->multiple()->preload()
                // TextInput::make('password')
                //         ->password()
                //         ->required()
                //         ->rules([Password::default()]),
                // TextInput::make('password_confirmation')
                //         ->password()
                //         ->required()
                //         ->same('password')
                //         ->rules([Password::default()])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('created_at')->date('d/m/Y H:i')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('change_password')
                ->form([
                    TextInput::make('password')
                        ->password()
                        ->required()
                        ->rules([Password::default()]),
                    TextInput::make('password_confirmation')
                        ->password()
                        ->required()
                        ->same('password')
                        ->rules([Password::default()])
                ])
                ->action(function (User $record, array $data) {
                    $record->update([
                        'password' => bcrypt($data['password'])
                    ]);
                    Filament::notify('success','Senha atualizada com sucesso!');
                }),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function canCreate() : bool  {
        return false;
    }
}
