from scripts.signature_planner import plan_signatures


def test_signature_planner_pads_to_multiple():
    result = plan_signatures(22, 4)
    assert result["total_pages"] == 24
    assert result["blank_pages_needed"] == 2


def test_signature_planner_sixteen_page_signature():
    result = plan_signatures(68, 16)
    assert result["total_pages"] == 80
    assert result["signature_count"] == 5
