@managing_users
Feature: Browsing users
    In order to see all users in the system
    As an varoius user roles
    I want to browse users

    Background:
        Given the system has roles with permisssions:
        | name | display_name | theme | permissions |
        | ROLE_ADMIN_SYSTEM | Admin system | owl/admin | owl_admin_admin_user_index |
        | ROLE_ADMIN_COMPANY | Admin company | owl/admin-company | owl_admin_admin_user_index |
        | ROLE_USER | User | owl/user |  |
        And there is an user system "admin_system@example.com" with role "Admin system"
        And there is also an user system "admin_company@example.com" with role "Admin company"
        And there is also an user system "user@example.com" with role "User"

    @ui
    Scenario Outline: Browsing users in system for diffrent roles
        Given there is an user system <email> with role <role>
        And I am logged in as <email> user
        When I want to browse users
        Then there should be <count_users> users in the list

    Examples:
        | email | role | count_users |
        | "admin_system_logged@example.com" | "Admin system" | 4 |
        | "admin_company_logged@example.com" | "Admin company" | 4 |