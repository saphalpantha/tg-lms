const path = require("path");

module.exports = {
  entry: {
    tg: "./src/front/index.js",
  },
  mode:"development",
  output: {
    filename: "[name].bundle.js",
    path: path.resolve(__dirname, "src/front/dist/js"),
    publicPath:path.resolve(__dirname, "src/front/dist/js"),
  },
  
    devServer: {
    contentBase: path.join(__dirname, 'src/front/dist/js'),
    hot: true,
    port: 3000,
    headers: {
      'Access-Control-Allow-Origin': '*',
    },
    proxy: {
      '/': {
        target: 'http://tg-lms.local',
        secure: false,
        changeOrigin: true,
      },
    },
  },
  module: {
    rules: [
      {
        test: /\.jsx?$/,
        exclude: /node_modules/,
        use: {
          loader: "babel-loader",
          options: {
            presets: ["@babel/preset-env", "@babel/preset-react"],

          },
        },
      },
     {
        test: /\.css$/,
        include:path.resolve(__dirname, "src/front/"),
        use: ['style-loader', 'css-loader', 'postcss-loader']
      },
    ],
  },
};




// const path = require("path");

// const webpack = require('webpack');
// module.exports = {
//   entry: {
//     tg: "./src/front/index.js", // Entry point for your application
//   },
//   mode: "development", // Development mode for hot reloading and source maps
//   output: {
//     filename: "[name].bundle.js", // Output filename for the compiled bundle
//     path: path.resolve(__dirname, "src/front/dist/js"), // Output directory for the bundle
//     publicPath: path.resolve(__dirname, "src/front/dist/js"), // Public path for assets referenced in the bundle (relative to HTML)
//   },
//   module: {
//     rules: [
//       {
//         test: /\.jsx?$/, // Rule for handling JavaScript and JSX files
//         exclude: /node_modules/, // Exclude node_modules directory
//         use: {
//           loader: "babel-loader", // Use Babel loader for transpiling
//           options: {
//             presets: ["@babel/preset-env", "@babel/preset-react"], // Babel presets for modern JavaScript and React
//           },
//         },
//       },
//       {
//         test: /\.css$/, // Rule for handling CSS files
//         include: path.resolve(__dirname, "src/front/"), // Include CSS files only from the src/front directory
//         use: ["style-loader", "css-loader", "postcss-loader"], // Loaders for processing CSS (style injection, CSS rules, and post-processing)
//       },
//     ],
//   },
//   devServer: {

//     static: path.join(__dirname, "src/front/dist/js"), // Serve static content from the output directory
//     hot: true, // Enable hot module replacement for live reloading
//     watchFiles: ['./src/front/**/*'],
//     port: 3000, // Development server port (default: 3000)
//     headers: {
//       "Access-Control-Allow-Origin": "*", // Allow requests from any origin (adjust for production)
//     },
//     proxy: [
//       {
//         context: "/", // Proxy all requests (adjust for specific routes)
//         target: "http://tg-lms.local", // Target URL for proxied requests
//         secure: false, // Allow non-HTTPS connections (adjust for production)
//         changeOrigin: true, // Modify request headers to reflect the target URL
//       },
//     ],

//   },
// };


