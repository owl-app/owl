@user_login
Feature: Signing in to the administration panel
    In order to manage the system
    As an admin system
    I want to be able to log in to the administration panel

    Background:
        Given the system has locale "en"
        And the system has role:
        | name | display_name | theme |
        | ROLE_ADMIN_SYSTEM | Admin system | owl/admin |
        And there is an user system "admin_system@example.com" with role "Admin system" and identified by "test123"

    @ui
    Scenario: Sign in with email and password
        When I want to log in
        And I specify the username as "admin_system@example.com"
        And I specify the password as "test123"
        And I log in
        Then I should be logged in

    @ui
    Scenario: Sign in with bad credentials
        When I want to log in
        And I specify the username as "admin@example.com"
        And I specify the password as "pswd"
        And I log in
        Then I should be notified about bad credentials
        And I should not be logged in
