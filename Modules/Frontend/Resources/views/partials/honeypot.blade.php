{{-- Spam trap: invisible to people, filled in by bots. See App\Http\Middleware\ProtectContactForms. --}}
<div style="position:absolute;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden;" aria-hidden="true">
    <label for="aldar-hp-{{ $id }}">Leave this field empty</label>
    <input type="text" id="aldar-hp-{{ $id }}" name="aldar_hp" value="" tabindex="-1" autocomplete="off">
</div>
