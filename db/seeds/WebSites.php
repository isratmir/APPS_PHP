<?php

use Phinx\Seed\AbstractSeed;

class WebSites extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $websites = json_decode(file_get_contents('/var/www/apps/db/seeds/websites.json'), 1);
        $websitesTable = $this->table('websites');
	      $websitesTable->insert($websites)->save();
    }
}
