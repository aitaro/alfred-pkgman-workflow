<?php
namespace WillFarrell\AlfredPkgMan;

require_once('Cache.php');
require_once('Repo.php');

class Pub extends Repo
{
    protected $id         = 'pub';
    protected $kind       = 'pub';
    protected $url        = 'https://pub.dev';

    protected $search_url = 'https://pub.dev/packages?q=';

    public function search($query)
    {
        if (!$this->hasMinQueryLength($query)) {
            return $this->xml();
        }

        $this->pkgs = $this->cache->get_query_json(
            $this->id,
            $query,
            "{$this->url}/api/search?q={$query}"
        )->packages;

        foreach ($this->pkgs as $pkg) {
            $title = $pkg->package;
            // $version = $pkg->version;
            $project_uri = "{$this->url}/packages/{$title}";
            $this->cache->w->result(
                $title,
                $this->makeArg($title, $pkg->project_uri),
                $title,
                "No additional info",
                "icon-cache/{$this->id}.png"
            );

            // only search till max return reached
            if (count($this->cache->w->results()) == $this->max_return) {
                break;
            }
        }

        $this->noResults($query, "{$this->search_url}{$query}");

        return $this->xml();
    }
}

// Test code, uncomment to debug this script from the command-line
// $repo = new Gems();
// echo $repo->search('cap');
