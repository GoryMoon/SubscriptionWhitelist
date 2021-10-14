<div class="form-group">
    <label for="steam_csv_link">CSV link (comma separated list)</label>
    <copy-link-component id="steam_csv_link" link="{{ $base_url }}steam_csv"></copy-link-component>
</div>
<div>
    <label for="steam_csv_example">CSV example</label>
    <textarea id="steam_csv_example" class="form-control text-monospace" cols="30" rows="1" readonly>76561298511410460,76561298522415443,76561298127111428,76561218837132421,76561228237434427</textarea>
</div>
<hr>
<div class="form-group">
    <label for="steam_nl_link">Newline link (newline separated list)</label>
    <copy-link-component id="steam_nl_link" link="{{ $base_url }}steam_nl"></copy-link-component>
</div>
<div>
    <label for="steam_nl_example">Newline example</label>
    <textarea id="steam_nl_example" class="form-control text-monospace" cols="30" rows="5" readonly>76561298511410460
76561298522415443
76561298127111428
76561218837132421
76561228237434427</textarea>
</div>
<hr>
<div class="form-group">
    <label for="steam_json_link">JSON array link</label>
    <copy-link-component id="steam_json_link" link="{{ $base_url }}steam_json_array"></copy-link-component>
</div>
<div class="form-group">
    <label for="steam_json_example">JSON array example</label>
    <textarea id="steam_json_example" class="form-control text-monospace" cols="30" rows="7" readonly>
[
    "76561298511410460",
    "76561298522415443",
    "76561298127111428",
    "76561218837132421",
    "76561228237434427"
]</textarea>
</div>
