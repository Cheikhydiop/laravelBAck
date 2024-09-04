<?php
// app/Scopes/TelephoneScope.php
namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TelephoneScope implements Scope
{
    protected $telephone;

    public function __construct($telephone)
    {
        $this->telephone = $telephone;
    }

    public function apply(Builder $builder, Model $model)
    {
        $builder->where('telephone', $this->telephone);
    }
}
