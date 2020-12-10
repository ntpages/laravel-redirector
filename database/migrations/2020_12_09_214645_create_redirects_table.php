<?php

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class CreateRedirectsTable extends Migration
{
    /**
     * @var string
     */
    private $config;

    public function __construct()
    {
        $this->config = config('redirector.database');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->config['table'] ?? 'redirects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('from_url', 2083); // the uniqueness is checked programmatically
            $table->string('to_url', 2083);

            // most likely will not be used but good to have
            $table->enum('status_code', [Response::HTTP_MOVED_PERMANENTLY, Response::HTTP_FOUND])
                ->default(Response::HTTP_MOVED_PERMANENTLY);

            // discards the redirects if false, follows if true, checks if null
            $table->boolean('healthy')->default(true);
            $table->timestamps();

            // relating to the user
            if ($this->config['auditable']) {
                $object = new $this->config['auditable'];

                if ($object instanceof Model) {
                    $tableName = $object->getTable();
                    $keyName = $object->getKeyName();
                    $colType = $object->getKeyType();

                    foreach (['created_by', 'updated_by'] as $colName) {
                        $table->addColumn($colType, $colName)->nullable();
                        $table->foreign($colName)->references($keyName)->on($tableName)->onDelete('SET NULL');
                    }
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->config['table'] ?? 'redirects');
    }
}
