<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\SpatieTagsEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = "heroicon-o-rectangle-stack";

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                SpatieMediaLibraryFileUpload::make("cover")->collection(
                    "cover"
                ),

                SpatieMediaLibraryFileUpload::make("gallery")
                    ->collection("gallery")
                    ->multiple(),

                TextInput::make("name")->label("Product Name"),

                TextInput::make("sku")
                    ->label("SKU")
                    ->unique(ignoreRecord: true),

                TextInput::make("slug")->unique(ignoreRecord: true),

                SpatieTagsInput::make("tags")
                    ->type("collection")
                    ->label("Collection"),

                TextInput::make("stock")->numeric()->default(0),
                TextInput::make("price")->numeric()->prefix("Rp"),
                TextInput::make("weight")->numeric()->suffix("gram"),
                // MarkdownEditor::make("description"),
                RichEditor::make("description"),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("name")->label("Product Name"),

                TextColumn::make("sku")->label("SKU"),

                TextColumn::make("slug"),
                TextColumn::make("stock")->suffix(" pcs"),
                TextColumn::make("price")->prefix("Rp."),
            ])
            ->filters([
                //
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            "index" => Pages\ListProducts::route("/"),
            "create" => Pages\CreateProduct::route("/create"),
            "edit" => Pages\EditProduct::route("/{record}/edit"),
        ];
    }
}
