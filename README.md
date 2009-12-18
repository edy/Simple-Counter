# Simple Counter

How to use the counter:

	require_once('counter.php');
	
	$config = array(
		'counter' => 'counter.dat',
		'ips' => 'ips.dat'
	);

	$counter = new Counter($config);

	echo $counter;


Short version:

	require_once('counter.php');
	
	echo new Counter;