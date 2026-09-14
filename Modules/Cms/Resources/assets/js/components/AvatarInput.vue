<template>
	<div :class="{'form-group': true, 'row': inline}">
		<label v-if="inputLabel" :for="inputId" :class="labelClass">{{label}}</label>
		<div :class="inputContainerClass">
            <div class="kt-avatar kt-avatar--outline" :id="inputId">
                <div class="kt-avatar__holder" :style="{'background-image': `url(${image})`}"></div>
                <label class="kt-avatar__upload" data-toggle="kt-tooltip" :title="addText">
                    <i class="fa fa-pen"></i>
                    <input 
                        type="file" 
                        :name="name" 
                        @change="onChange"
                    >
                </label>
                <span class="kt-avatar__cancel" data-toggle="kt-tooltip" :title="removeText">
                    <i class="fa fa-times"></i>
                </span>
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
		"help",
		"id",
		"name",
		"inline",
        "addText",
        "removeText",
        "image"
	],
	data: function () {
		return {

		}
	},
	mounted: function () {
		let avatar = new KTAvatar(this.inputId)
	},
	computed: {
		inputId () {
			return this.id ? this.id : this.name
		},
		inputLabel () {
			return this.label ? this.label : ""
		},
		labelClass () {
			return this.inline ? `col-form-label col-lg-${this.inline.charAt(0)}` : ''
		},
		inputContainerClass () {
			return this.inline ? `col-lg-${this.inline.charAt(2)}` : ''
		}
	},
	methods: {
		onChange(e) {
			this.$emit('input', e.target.files[0])
		}
	}
}
</script>