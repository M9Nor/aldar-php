<template>
	<div :class="{'form-group': true, 'row': inline, 'validated': form.errors.has(name)}">
		<label v-if="inputLabel" :for="inputId" :class="labelClass">{{label}}</label>
		<div :class="inputContainerClass">
			<div :class="{'input-group': password && showable, 'kt-input-icon kt-input-icon--right': icon}">
				<div v-if="password && showable" class="input-group-prepend">
					<button class="btn btn-secondary btn-icon toggle-password" type="button" @mousedown="show" @mouseup="hide" @mouseleave="hide">
						<i :class="hidden ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
					</button>
				</div>
				<input 
					:type="thisType" 
					:name="name" 
					:class="{'form-control': true, 'is-invalid': form.errors.has(name)}" 
					:id="inputId" 
					v-model="thisValue" 
					@input="onInput($event.target.value)"
					:placeholder="thisPlaceholder"
					:autocomplete="password ? 'new-password' : name"
				>
				<span v-if="icon" class="kt-input-icon__icon kt-input-icon__icon--right">
					<span><i :class="icon"></i></span>
				</span>
				<div v-if="form.errors.has(name)" class="invalid-feedback">
					<ul v-for="error in form.errors.get(name)">
						<li v-text="error"></li>
					</ul>
				</div>
			</div>
			<span v-if="help" class="form-text text-muted">{{help}}</span>
		</div>
	</div>
</template>

<script>
export default {
	name: "TextInput",
	props: [
		"label",
		"placeholder",
		"help",
		"value",
		"id",
		"name",
		"type",
		"icon",
		"inline",
		"showable",
		"form"
	],
	data: function () {
		return {
			hidden: true,
			password: false,
			thisType: this.type ? this.type : "text",
			thisValue: this.value
		}
	},
	created: function() {
		if(this.type == 'password')
		{
			this.password = true;
		}
		console.log(this.form.errors.has(name))
	},
	updated: function() {
		console.log(this.form.errors.has(name))
	},
	computed: {
		inputId () {
			return this.id ? this.id : this.name
		},
		inputLabel () {
			return this.label ? this.label : ""
		},
		inputType () {
			return this.type ? this.type : "text"
		},
		inputValue () {
			return this.value ? this.value : ""
		},
		thisPlaceholder () {
			return this.placeholder ? this.placeholder : ""
		},
		labelClass () {
			return this.inline ? `col-form-label col-lg-${this.inline.charAt(0)}` : ''
		},
		inputContainerClass () {
			let output = ''
			output += this.inline ? `col-lg-${this.inline.charAt(2)}` : ''
			return output
		}
	},
	methods: {
		onInput (value) {
			this.$emit("input", value)
		},
		show () {
			this.hidden 	= false
			this.thisType 	= 'text'
		},
		hide () {
			this.hidden 	= true
			this.thisType 	= 'password'
		}
	}
}
</script>