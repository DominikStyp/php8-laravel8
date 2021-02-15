<?php


namespace Tests;


interface ResourceControllerTestInterface {
    public function test_index_GET();
    public function test_create_GET();
    public function test_store_POST();
    public function test_show_GET();
    public function test_edit_GET();
    public function test_update_PUT_PATCH();
    public function test_destroy_DELETE();
}
