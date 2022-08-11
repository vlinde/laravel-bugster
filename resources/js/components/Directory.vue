<template>
  <div>
    <heading class="mb-6">Directory: {{ $route.params.directory_name }}</heading>

    <div class="card">
      <div class="flex items-center p-3 border-b border-50">
        <router-link :to="{ name: 'bugster-log-files' }"
                     class="no-underline dim text-90">
          < Back
        </router-link>
      </div>

      <div v-if="files.length > 0" class="flex flex-wrap">
        <div v-for="file in files" class="p-3 files-card">
          <a :href="file.download_link"
             class="flex justify-between items-center overflow-hidden rounded-lg border border-50 p-4 hover:shadow-md cursor-pointer no-underline text-90"
             title="Download Log"
             download>
            <div>
              {{ file.name }} <br>
              Size: {{ file.size }}MB
            </div>
            <div>
              <svg class="files-card-icon" enable-background="new 0 0 330 330" version="1.1" viewBox="0 0 330 330"
                   xml:space="preserve"
                   xmlns="http://www.w3.org/2000/svg" fill="#3c4b5f">
	              <path
                    d="m154.39 255.6c0.351 0.351 0.719 0.683 1.103 0.998 0.169 0.138 0.347 0.258 0.52 0.388 0.218 0.164 0.432 0.333 0.659 0.484 0.212 0.142 0.432 0.265 0.649 0.395 0.202 0.121 0.4 0.248 0.608 0.359 0.223 0.12 0.453 0.221 0.681 0.328 0.215 0.102 0.427 0.21 0.648 0.301 0.223 0.092 0.45 0.167 0.676 0.247 0.236 0.085 0.468 0.175 0.709 0.248 0.226 0.068 0.456 0.119 0.684 0.176 0.246 0.062 0.489 0.131 0.739 0.181 0.263 0.052 0.529 0.083 0.794 0.121 0.219 0.031 0.435 0.073 0.658 0.095 0.492 0.048 0.986 0.075 1.48 0.075s0.988-0.026 1.479-0.075c0.226-0.022 0.444-0.064 0.667-0.096 0.262-0.037 0.524-0.068 0.784-0.12 0.255-0.05 0.504-0.121 0.754-0.184 0.223-0.057 0.448-0.105 0.669-0.172 0.246-0.075 0.483-0.167 0.724-0.253 0.221-0.08 0.444-0.152 0.662-0.242 0.225-0.093 0.44-0.202 0.659-0.306 0.225-0.106 0.452-0.206 0.672-0.324 0.21-0.112 0.408-0.239 0.611-0.361 0.217-0.13 0.437-0.252 0.648-0.394 0.222-0.148 0.431-0.314 0.644-0.473 0.179-0.134 0.362-0.258 0.536-0.4 0.365-0.3 0.714-0.617 1.049-0.949 0.016-0.016 0.034-0.028 0.049-0.044l70.002-69.998c5.858-5.858 5.858-15.355 0-21.213-5.857-5.857-15.355-5.858-21.213-1e-3l-44.396 44.393v-183.79c0-8.284-6.716-15-15-15s-15 6.716-15 15v183.78l-44.392-44.391c-5.857-5.858-15.355-5.858-21.213 0s-5.858 15.355 0 21.213l69.997 69.995z"/>
                <path
                    d="m315 160c-8.284 0-15 6.716-15 15v115h-270v-115c0-8.284-6.716-15-15-15s-15 6.716-15 15v130c0 8.284 6.716 15 15 15h300c8.284 0 15-6.716 15-15v-130c0-8.284-6.716-15-15-15z"/>
              </svg>
            </div>
          </a>
        </div>
      </div>
      <div v-else class="flex flex-wrap p-4">
        This directory is empty
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return {
      files: []
    }
  },
  mounted() {
    this.getFiles();
  },
  methods: {
    getFiles() {
      axios.get(`/nova-vendor/vlinde/bugster/log-files/?path=${this.$route.params.directory_path}`)
          .then(({data}) => {
            this.files = data.files
          })
    }
  }
}
</script>

<style>
.files-card {
  width: 25%;
}

.files-card-icon {
  width: 20px;
  vertical-align: middle;
}
</style>
