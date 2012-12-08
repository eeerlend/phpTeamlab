<html>
<body>
<?php require_once('TeamLab.php'); ?>

<?php
try {
    // TeamLab::logout();
    
    TeamLab::initWith(
        'USERNAME', // username (email)
        'PASSWORD', // password
        'PORALNAME.teamlab.com' // <portalname>.teamlab.com
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
     
    // Creates a new crm-contact in your portal
    $newPerson = TeamLab::post('api/1.0/crm/contact/person', array(
        'isPrivate' => 'true',
        'firstName' => 'John',
        'lastName' => 'Doe',
    ));
    
    // Dump the data
    echo '<h1>Example POST call</h1>';
    echo '<pre>';
    var_dump($newPerson);
    echo '</pre>';
    
} catch (Exception $e) {
    echo '<h1></h1>Caught exception:<br />',  $e->getMessage(), "\n";
}
?>
</body>
</html>