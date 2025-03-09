@managing_users
Feature: Adding a new user
    In order to create new user account
    As an Admin system
    I want to add a user to the system

    Background:
        Given the system has locale "en"
        And the system has role:
        | name | display_name | theme |
        | ROLE_ADMIN_SYSTEM | Admin system | owl/admin |
        And this role has a permissions:
        | route |
        | owl_admin_admin_user_index |
        | owl_admin_admin_user_create |
        And there is an user system "admin_system@example.com" with role "Admin system"
        And I am logged in as "admin_system@example.com" user

    @ui @javascript
    Scenario: Adding a new user
        When I want to create a new user
        And I specify its email as "l.skywalker@gmail.com"
        And I specify its password as "lightsaber"
        And I specify its display name as "Luke"
        And I specify its locale as "English"
        And I add it
        Then I should be notified that it has been successfully created
        And the administrator "l.skywalker@gmail.com" should appear in the store

    @ui @javascript
    Scenario: Adding a new administrator and log in with its credentials
        When I want to create a new user
        And I specify its email as "l.skywalker@gmail.com"
        And I specify its display name as "Luke"
        And I specify its password as "lightsaber"
        And I specify its locale as "English"
        And I enable it
        And I add it
        Then I should be able to log in as "l.skywalker@gmail.com" authenticated by "lightsaber" password
