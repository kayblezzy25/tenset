"""
Catalog of skills/services you sell on the site.

EDIT THIS FILE to describe what you actually offer. Add as many entries
to PRODUCTS as you like -- the homepage renders one card per entry.
"""

PRODUCTS = [
    {
        "id": "skill-1",
        "name": "EDIT ME: Your Skill or Service Name",
        "description": (
            "EDIT ME: Describe what the buyer gets -- e.g. a 1-on-1 coaching "
            "call, a finished design, a course, mentorship, etc."
        ),
        "price_usd": 50.00,
        "price_ngn": 75000.00,
    },
]


def get_product(product_id):
    for product in PRODUCTS:
        if product["id"] == product_id:
            return product
    return None
