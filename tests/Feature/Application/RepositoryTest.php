<?php

use Application\Data\DataEntity;
use Application\Repository\Repository;
use Illuminate\Database\Eloquent\Model;

function positionRepository(): Repository
{
    $model = new class extends Model
    {
        protected $table = 'positions';

        protected $guarded = [];
    };

    return new class($model) extends Repository {};
}

function positionData(string $name): DataEntity
{
    return new class($name) extends DataEntity
    {
        public function __construct(private readonly string $name) {}

        public function toArray(): array
        {
            return ['name' => $this->name];
        }
    };
}

it('returns every row when a previous call filtered the query', function () {
    $repository = positionRepository();
    $repository->create(positionData('Developer'));
    $repository->create(positionData('Designer'));

    $repository->query()->where('name', 'Developer')->get();

    expect($repository->all())->toHaveCount(2);
});

it('does not carry select columns over to the next call', function () {
    $repository = positionRepository();
    $repository->create(positionData('Developer'));

    $repository->query()->select('name')->get();

    expect($repository->all()->first()->getAttributes())->toHaveKey('id');
});

it('finds, updates and deletes by id', function () {
    $repository = positionRepository();
    $repository->create(positionData('Developer'));
    $id = (string) $repository->all()->first()->id;

    $repository->update($id, positionData('Engineer'));
    expect($repository->find($id)->name)->toBe('Engineer');

    $repository->delete($id);
    expect($repository->all())->toHaveCount(0);
});
