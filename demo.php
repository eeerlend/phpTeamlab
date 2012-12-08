<html>
<body>
<?php
try {
    require_once('TeamLab.php');
    TeamLab::initWith(
        'USERNAME', // username (email)
        'PASSWORD', // password
        'PORAL.teamlab.com' // <portalname>.teamlab.com
        );
    /* 
     * GET
     * Example: http://api.teamlab.com/docs/people/get/api/1.0/people
     */
     
    // List all users on your portal
    $people = TeamLab::get('api/1.0/people');
    
    // Dump the data
    echo '<h1>Example GET call</h1>';
    echo '<pre>';
    var_dump($people);
    echo '</pre>';
    
    /* 
     * POST
     * Example: http://api.teamlab.com/docs/crm/get/api/1.0/crm/contact
     */
         
    // Uncomment this to create a contact
    /* $newPerson = TeamLab::post('api/1.0/crm/contact/person', array(
        'isPrivate' => 'true',
        'firstName' => 'John',
        'lastName' => 'Doe',
    )); */
    
    echo '<h1>Example POST call</h1>';
    
    // Dump the data
    if (is_object($newPerson)) {
        echo '<pre>';
        var_dump($newPerson);
        echo '</pre>';
    } else {
        echo '<p>Uncomment the POST-call in demo.php to actually create a contact in your portal</p>';
    }
    
    
} catch (Exception $e) {
    echo '<h1></h1>Caught exception:<br />',  $e->getMessage(), "\n";
}
?>
</body>
</html>