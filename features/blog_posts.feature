Feature: Manage Blog post
  @createSchema @BlogPost @Comment
  Scenario: Create a blog post
    Given I am authenticated as "writer"
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/blog_posts" with body:
    """
    {
      "title": "Hello a title",
      "content": "The content is suppose to be at least 20 characters",
      "slug": "a-new-slug"
    }
    """
    Then the response status code should be 201
    And the response should be in JSON
    And the JSON matches expected template:
    """
    {
      "@context": "/api/contexts/BlogPost",
      "@id": "@string@",
      "@type": "BlogPost",
      "comments": [],
      "id": @integer@,
      "title": "Hello a title",
      "content": "The content is suppose to be at least 20 characters",
      "slug": "a-new-slug",
      "published": "@string@.isDateTime()",
      "author": "@string@",
      "images": []
    }
    """

  @Comment
  Scenario: Add comment to the new blog post
    Given I am authenticated as "writer"
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/comments" with body:
    """
    {
      "content": "It's a first comment published to this post?",
      "blogPost": "/api/blog_posts/101"
    }
    """
    # And print last response # debug
    Then the response status code should be 201
    And the response should be in JSON
    And the JSON matches expected template:
    """
    {
      "@context": "/api/contexts/Comment",
      "@id": "@string@",
      "@type": "Comment",
      "id": @integer@,
      "content": "It\u0027s a first comment published to this post?",
      "published": "@string@.isDateTime()",
      "author": "@string@",
      "blogPost": "/api/blog_posts/101"
    }
    """

  @Comment
  Scenario: Throws error when comment is invalid
    Given I am authenticated as "writer"
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/comments" with body:
    """
    {
      "content": "",
      "blogPost": "/api/blog_posts/105"
    }
    """
    Then the response status code should be 400
    And the response should be in JSON
    And the JSON matches expected template:
    """
    {
        "@context": "/api/contexts/ConstraintViolationList",
        "@type": "ConstraintViolationList",
        "hydra:title": "An error occurred",
        "hydra:description": "Item not found for \"/api/blog_posts/105\".",
        "violations": [
            {
                "propertyPath": "",
                "message": "Item not found for \"/api/blog_posts/105\"."
            }
        ]
    }
    """

  @createSchema @BlogPost
  Scenario: Throws an error when blog post is invalid
    Given I am authenticated as "writer"
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/blog_posts" with body:
    """
    {
      "title": "",
      "content": "",
      "slug": "a-new-slug"
    }
    """
    Then the response status code should be 400
    And the response should be in JSON
    And the JSON matches expected template:
    """
    {
      "@context":"\/api\/contexts\/ConstraintViolationList",
      "@type":"ConstraintViolationList",
      "hydra:title":"An error occurred",
      "hydra:description":"@string@",
      "violations":[
        {
          "propertyPath":"title",
          "message":"@string@"
        },
        {
          "propertyPath":"content",
          "message":"@string@"
        }
      ]
    }
    """

  Scenario: Throws an error when user is not authenticated
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/blog_posts" with body:
    """
    {
      "title": "",
      "content": "",
      "slug": "a-new-slug"
    }
    """
    Then the response status code should be 401
