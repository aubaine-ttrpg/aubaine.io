from scripts.export_preflight import parse_size, pt_to_in


def test_parse_size_inches():
    width, height = parse_size("6.25x9.25in")
    assert round(pt_to_in(width), 2) == 6.25
    assert round(pt_to_in(height), 2) == 9.25


def test_parse_size_mm():
    width, height = parse_size("210x297mm")
    assert round(pt_to_in(width), 2) == 8.27
    assert round(pt_to_in(height), 2) == 11.69
