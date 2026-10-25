<?php

it('reports that the application is up', function () {
    $this->get('/up')->assertOk();
});
