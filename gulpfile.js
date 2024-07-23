const del = require('del')
const zip = require('gulp-zip')
const { series, watch, src, dest, parallel } = require('gulp')

// style
const autoprefixer = require('autoprefixer')
const cssnano = require('cssnano')
const sass = require('gulp-sass')(require('sass'))
const postcss = require('gulp-postcss')
const rename = require('gulp-rename')
const cmq  = require('postcss-combine-media-query')

// script
const rollup = require('rollup').rollup
const { nodeResolve } = require('@rollup/plugin-node-resolve')
const { babel } = require('@rollup/plugin-babel')
const commonjs = require('@rollup/plugin-commonjs')
const { terser } =  require('rollup-plugin-terser')

// style
const style = (name, minify = false) => {
  const plugins = [
    autoprefixer(),
    cmq()
  ]

  if (minify) {
    plugins.push(cssnano())
  }

  return src(`assets/src/styles/${name}.scss`)
    .pipe(sass())
    .pipe(postcss(plugins))
    .pipe(rename({
      suffix: minify ? '.min' : ''
    }))
    .pipe(dest('assets/dist'))
}

// script
const script = async (name, format, minify) => {
  const input = `assets/src/scripts/${name}.js`
  const file = filename(name, format, minify)
  const isDefault = format === 'umd' || format === 'iife'
  const plugins = [
    nodeResolve(),
    babel({ babelHelpers: 'bundled' }),
    commonjs()
  ]

  const inputOptions = { input, plugins }

  const outputOptions = {
    file,
    format
  }

  if (minify) {
    plugins.push(terser())
  }

  if (isDefault && !minify) {
    outputOptions.sourcemap = true
  }

  const bundle = await rollup(inputOptions)

  await bundle.write(outputOptions)
}

const filename = (name, format, minify) => {
  if (format === 'umd' || format === 'iife') {
    return `assets/dist/${name}${minify ? '.min' : ''}.js`
  }

  return `assets/dist/${name}.${format}${minify ? '.min' : ''}.js`
}

const scripts = parallel(
  () => script('main', 'umd'),
  () => script('main', 'umd', true),

  () => script('admin', 'umd'),
  () => script('admin', 'umd', true)
)

const styles = parallel(
  () => style('main'),
  () => style('main', true),
  () => style('admin'),
  () => style('admin', true)
)

// general
const clean = () => del('assets/dist')

const serve = () => {
  watch('assets/src/scripts/**/*.js', scripts)
  watch('assets/src/styles/**/*', styles)
}

const buildZip = () => {
  const pkg = require('./package.json')

  return src([
    './**/*',
    '!**/*.log',
    '!./node_modules/**',
    `!${pkg.name}.zip`
  ], {
    allowEmpty: true
  })
    .pipe(zip(`${pkg.name}.zip`))
    .pipe(dest('.'))
}

exports.serve = series(clean, styles, scripts, serve)
exports.build = series(clean, styles, scripts)
exports.buildZip = series(clean, styles, scripts, buildZip)
