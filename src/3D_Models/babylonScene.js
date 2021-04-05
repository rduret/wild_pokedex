const canvas = document.getElementById("renderCanvas"); // Get the canvas element
const engine = new BABYLON.Engine(canvas, true); // Generate the BABYLON 3D engine


//***PG */


//Scene and camera
var createScene = function () {
    var scene = new BABYLON.Scene(engine);
    var camera = new BABYLON.ArcRotateCamera("Camera", Math.PI / 3,  Math.PI / 2.7, 3, new BABYLON.Vector3(0,0.5,0), scene);
    camera.attachControl(canvas, true);

    //function to import model into scene
    function loadMeshes(){
        BABYLON.SceneLoader.ImportMeshAsync("", "/", "eevee.glb")
        .then((result) => {
            var meshes = new Array();
            meshes = result.meshes;
            //Add shadow caster to each mesh within model
            meshes.forEach(element => shadowGenerator.addShadowCaster(element,true));
            console.log(meshes);
            })
    };

    loadMeshes();

    //Setup environment
    var env = scene.createDefaultEnvironment({ 
        createSkybox: true,
        skyboxSize: 150,
        skyboxColor: BABYLON.Color3.White(),
        createGround: true,
        groundSize: 5,
        groundColor: BABYLON.Color3.White(),
        enableGroundShadow: true,
        groundYBias: 10
    });

    //Lights
    var dLight = new BABYLON.DirectionalLight("dLight", new BABYLON.Vector3(0.02,-0.05,-0.05), scene);
    dLight.position = new BABYLON.Vector3(0,20,0);
    var pLight = new BABYLON.PointLight("pLight", new BABYLON.Vector3(5,10,-5), scene);
    pLight.diffuse = new BABYLON.Color3(0.53, 0.66, 0.74);
    pLight.specular = new BABYLON.Color3(0.83, 0.86, 0.89);

    //Shadows
    var shadowGenerator = new BABYLON.ShadowGenerator(2048, dLight);
    shadowGenerator.useBlurExponentialShadowMap = true;

    return scene;

};

//***/PG */

const scene = createScene(); //Call the createScene function
        
        // Register a render loop to repeatedly render the scene
        engine.runRenderLoop(function () {
                scene.render();
        });


        // Watch for browser/canvas resize events
        window.addEventListener("resize", function () {
                engine.resize();
        });