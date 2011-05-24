# What is this?

Munin is a fantastic tool, but it's a hassle to create new graphs. This library lets you build new munin graphs by writing some PHP code- it handles everything else for you.

# Example


    munin::graph(function($graph) {
	
    	$graph->title = "Load Average";
    	$graph->category = "system";
    	$graph->scale = true;
	
    	$graph->info = "The load average of the server";
    	$graph->vlabel = "load average";
	
	
    	$load = $graph->collector('load');
    	$load->min = 0;
	
    	$graph->collect(function($load) {
    		$load->value = `cut -d' ' -f2  /proc/loadavg`;
    	});
    });

Put the above code into a file called `graph.php`-

When called as `php graph.php config`

    graph_title Load Average
    graph_category system
    graph_scale yes
    graph_info The load average of the server
    graph_vlabel load average
    load.label load
    load.min 0

When called as `php graph.php`

    load.value 1.24
    

# What doesn't it do?

This library is super-beta right now. It should work for most simple to intermediate graphs. Multigraph probably won't work yet. graph_args is likely to be a little bit wonky. I'll probably end up changing the syntax a little bit.

I was going to make this more DSL-ish, but I can't really do that without polluting the global namespace with a bunch of generically named functions, so I decided to go with object instead.

# Dependencies

You'll need PHP 5.3 because I use closures. Get with the times!