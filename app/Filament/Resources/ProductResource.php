<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, $set){
                            $state = Str::slug($state);
                            $set('slug',$state);
                        })
                        ->label('Nome Produto'),
                TextInput::make('description')->label('Descrição'),
                TextInput::make('price')->required()->label('Preço'),
                TextInput::make('amount')->required()->label('Quantidade'),
                TextInput::make('slug')->disabled(),
                FileUpload::make('photo')
                        ->image()
                        ->directory('products'),
                Select::make('categories')->relationship('categories','name')->multiple()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')->circular()->height(40),
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('price')
                        ->sortable()
                        ->searchable()
                        ->money('BRL'),
                TextColumn::make('amount'),
                TextColumn::make('created_at')->date('d/m/Y H:i:s'),
            ])
            ->filters([
                Filter::make('amount')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->where('amount', '>', 9))
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at','DESC');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CategoriesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
