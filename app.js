// Question Bank - All GED Subjects
const questionBank = {
    math: [
        {
            id: 1,
            question: "If 3x + 7 = 22, what is the value of x?",
            options: ["x = 3", "x = 5", "x = 7", "x = 9"],
            correct: 1,
            explanation: "Subtract 7 from both sides: 3x = 15, then divide by 3: x = 5"
        },
        {
            id: 2,
            question: "What is 25% of 80?",
            options: ["15", "20", "25", "30"],
            correct: 1,
            explanation: "25% = 0.25, so 0.25 × 80 = 20"
        },
        {
            id: 3,
            question: "A rectangle has a length of 12 cm and a width of 5 cm. What is its area?",
            options: ["17 cm²", "34 cm²", "60 cm²", "120 cm²"],
            correct: 2,
            explanation: "Area = length × width = 12 × 5 = 60 cm²"
        },
        {
            id: 4,
            question: "What is the value of 2³ + 4²?",
            options: ["20", "24", "28", "32"],
            correct: 1,
            explanation: "2³ = 8 and 4² = 16, so 8 + 16 = 24"
        },
        {
            id: 5,
            question: "If a shirt costs $45 and is on sale for 20% off, what is the sale price?",
            options: ["$25", "$30", "$36", "$40"],
            correct: 2,
            explanation: "20% of $45 = $9, so $45 - $9 = $36"
        },
        {
            id: 6,
            question: "What is the slope of a line passing through points (2, 3) and (6, 11)?",
            options: ["1", "2", "3", "4"],
            correct: 1,
            explanation: "Slope = (y₂ - y₁)/(x₂ - x₁) = (11 - 3)/(6 - 2) = 8/4 = 2"
        },
        {
            id: 7,
            question: "A circle has a radius of 7 cm. What is its circumference? (Use π ≈ 3.14)",
            options: ["21.98 cm", "43.96 cm", "153.86 cm", "307.72 cm"],
            correct: 1,
            explanation: "Circumference = 2πr = 2 × 3.14 × 7 = 43.96 cm"
        },
        {
            id: 8,
            question: "What is the median of the following set: 5, 12, 8, 15, 9?",
            options: ["8", "9", "10", "12"],
            correct: 1,
            explanation: "Arrange in order: 5, 8, 9, 12, 15. The middle value is 9"
        },
        {
            id: 9,
            question: "If y = 2x - 3, what is y when x = 5?",
            options: ["5", "7", "9", "11"],
            correct: 1,
            explanation: "y = 2(5) - 3 = 10 - 3 = 7"
        },
        {
            id: 10,
            question: "What is 3/4 + 2/3?",
            options: ["5/7", "17/12", "5/12", "1"],
            correct: 1,
            explanation: "Find common denominator 12: 9/12 + 8/12 = 17/12"
        },
        {
            id: 11,
            question: "A box contains 5 red balls, 3 blue balls, and 2 green balls. What is the probability of randomly selecting a blue ball?",
            options: ["1/10", "3/10", "1/3", "1/2"],
            correct: 1,
            explanation: "Total balls = 10, blue balls = 3, so probability = 3/10"
        },
        {
            id: 12,
            question: "What is the volume of a cube with side length 4 cm?",
            options: ["16 cm³", "48 cm³", "64 cm³", "256 cm³"],
            correct: 2,
            explanation: "Volume = side³ = 4³ = 64 cm³"
        },
        {
            id: 13,
            question: "Solve for x: 2(x + 3) = 18",
            options: ["x = 3", "x = 6", "x = 9", "x = 12"],
            correct: 1,
            explanation: "2x + 6 = 18, so 2x = 12, therefore x = 6"
        },
        {
            id: 14,
            question: "What is 15% of 200?",
            options: ["20", "25", "30", "35"],
            correct: 2,
            explanation: "15% = 0.15, so 0.15 × 200 = 30"
        },
        {
            id: 15,
            question: "The mean of 5 numbers is 12. What is their sum?",
            options: ["17", "48", "60", "72"],
            correct: 2,
            explanation: "Mean = Sum/Count, so Sum = Mean × Count = 12 × 5 = 60"
        },
        {
            id: 16,
            question: "What is the value of √144?",
            options: ["10", "11", "12", "13"],
            correct: 2,
            explanation: "12 × 12 = 144, so √144 = 12"
        },
        {
            id: 17,
            question: "If a car travels 240 miles in 4 hours, what is its average speed?",
            options: ["50 mph", "55 mph", "60 mph", "65 mph"],
            correct: 2,
            explanation: "Speed = Distance/Time = 240/4 = 60 mph"
        },
        {
            id: 18,
            question: "What is the perimeter of a square with side length 9 cm?",
            options: ["18 cm", "27 cm", "36 cm", "81 cm"],
            correct: 2,
            explanation: "Perimeter = 4 × side = 4 × 9 = 36 cm"
        },
        {
            id: 19,
            question: "Simplify: 5² - 3²",
            options: ["4", "8", "16", "32"],
            correct: 2,
            explanation: "5² = 25 and 3² = 9, so 25 - 9 = 16"
        },
        {
            id: 20,
            question: "What is 0.75 expressed as a fraction in simplest form?",
            options: ["3/5", "2/3", "3/4", "4/5"],
            correct: 2,
            explanation: "0.75 = 75/100 = 3/4 when simplified"
        },
        {
            id: 21,
            question: "If the ratio of boys to girls in a class is 3:2 and there are 15 boys, how many girls are there?",
            options: ["8", "10", "12", "15"],
            correct: 1,
            explanation: "3:2 = 15:x, so 3x = 30, therefore x = 10"
        },
        {
            id: 22,
            question: "What is the area of a triangle with base 8 cm and height 6 cm?",
            options: ["14 cm²", "24 cm²", "28 cm²", "48 cm²"],
            correct: 1,
            explanation: "Area = (1/2) × base × height = (1/2) × 8 × 6 = 24 cm²"
        },
        {
            id: 23,
            question: "Evaluate: |−7|",
            options: ["−7", "0", "7", "14"],
            correct: 2,
            explanation: "The absolute value of -7 is 7"
        },
        {
            id: 24,
            question: "What is 2/5 of 50?",
            options: ["10", "15", "20", "25"],
            correct: 2,
            explanation: "(2/5) × 50 = 100/5 = 20"
        },
        {
            id: 25,
            question: "If x² = 49, what are the possible values of x?",
            options: ["7 only", "−7 only", "7 and −7", "49 and −49"],
            correct: 2,
            explanation: "Both 7² and (−7)² equal 49"
        },
        {
            id: 26,
            question: "What is the next number in the sequence: 2, 6, 12, 20, ...?",
            options: ["28", "30", "32", "36"],
            correct: 1,
            explanation: "Pattern: add 4, 6, 8, 10... Next is +12, so 20 + 12 = 32. Wait, let me recalculate: differences are 4, 6, 8, so next is 10, giving 20 + 10 = 30"
        },
        {
            id: 27,
            question: "A store marks up items by 40%. If an item costs the store $50, what is the selling price?",
            options: ["$60", "$65", "$ 70", "$75"],
            correct: 2,
            explanation: "40% of $50 = $20, so selling price = $50 + $20 = $70"
        },
        {
            id: 28,
            question: "What is the greatest common factor (GCF) of 24 and 36?",
            options: ["4", "6", "8", "12"],
            correct: 3,
            explanation: "Factors of 24: 1,2,3,4,6,8,12,24. Factors of 36: 1,2,3,4,6,9,12,18,36. GCF = 12"
        },
        {
            id: 29,
            question: "If 5x - 2 = 3x + 10, what is x?",
            options: ["4", "5", "6", "7"],
            correct: 2,
            explanation: "5x - 3x = 10 + 2, so 2x = 12, therefore x = 6"
        },
        {
            id: 30,
            question: "What is the mode of the data set: 4, 7, 4, 9, 4, 5, 7?",
            options: ["4", "5", "7", "9"],
            correct: 0,
            explanation: "The mode is the most frequent value, which is 4 (appears 3 times)"
        }
    ],
    language: [
        {
            id: 1,
            question: "Which sentence is grammatically correct?",
            options: [
                "The team are playing well today.",
                "The team is playing well today.",
                "The team were playing well today.",
                "The team be playing well today."
            ],
            correct: 1,
            explanation: "'Team' is a collective noun treated as singular, so 'is' is correct"
        },
        {
            id: 2,
            question: "Choose the correct word: The weather was _____ cold yesterday.",
            options: ["to", "too", "two", "tue"],
            correct: 1,
            explanation: "'Too' means 'excessively' or 'also'"
        },
        {
            id: 3,
            question: "Identify the subject in this sentence: 'The quick brown fox jumps over the lazy dog.'",
            options: ["quick", "fox", "jumps", "dog"],
            correct: 1,
            explanation: "The subject is 'fox' - the one performing the action"
        },
        {
            id: 4,
            question: "Which word is a synonym for 'happy'?",
            options: ["sad", "joyful", "angry", "tired"],
            correct: 1,
            explanation: "'Joyful' means the same as 'happy'"
        },
        {
            id: 5,
            question: "Choose the correct punctuation: 'I need to buy eggs milk and bread'",
            options: [
                "I need to buy eggs milk and bread.",
                "I need to buy eggs, milk, and bread.",
                "I need to buy eggs; milk; and bread.",
                "I need to buy eggs: milk: and bread."
            ],
            correct: 1,
            explanation: "Items in a list should be separated by commas"
        },
        {
            id: 6,
            question: "What is the past tense of 'run'?",
            options: ["runned", "ran", "runs", "running"],
            correct: 1,
            explanation: "'Ran' is the correct past tense of 'run'"
        },
        {
            id: 7,
            question: "Which sentence uses the apostrophe correctly?",
            options: [
                "The dogs bone is buried.",
                "The dog's bone is buried.",
                "The dogs' bone is buried.",
                "The dogs bone's is buried."
            ],
            correct: 1,
            explanation: "The apostrophe shows possession: the bone belongs to one dog"
        },
        {
            id: 8,
            question: "Choose the correct word: 'She did _____ homework last night.'",
            options: ["her", "she", "hers", "herself"],
            correct: 0,
            explanation: "'Her' is the possessive pronoun needed here"
        },
        {
            id: 9,
            question: "What type of sentence is this: 'Stop!'",
            options: ["Declarative", "Interrogative", "Imperative", "Exclamatory"],
            correct: 2,
            explanation: "An imperative sentence gives a command"
        },
        {
            id: 10,
            question: "Which word is spelled correctly?",
            options: ["recieve", "receive", "receve", "receeve"],
            correct: 1,
            explanation: "'Receive' follows the 'i before e except after c' rule"
        },
        {
            id: 11,
            question: "Identify the verb in this sentence: 'The children played in the park.'",
            options: ["children", "played", "in", "park"],
            correct: 1,
            explanation: "'Played' is the action word (verb)"
        },
        {
            id: 12,
            question: "Choose the correct form: 'Between you and _____'",
            options: ["I", "me", "myself", "mine"],
            correct: 1,
            explanation: "After a preposition like 'between', use the object pronoun 'me'"
        },
        {
            id: 13,
            question: "What is the main idea of a paragraph?",
            options: [
                "The first sentence",
                "The last sentence",
                "The central point or message",
                "The longest sentence"
            ],
            correct: 2,
            explanation: "The main idea is the central point the paragraph conveys"
        },
        {
            id: 14,
            question: "Which sentence is in passive voice?",
            options: [
                "The cat chased the mouse.",
                "The mouse was chased by the cat.",
                "The cat is chasing the mouse.",
                "The cat will chase the mouse."
            ],
            correct: 1,
            explanation: "In passive voice, the subject receives the action"
        },
        {
            id: 15,
            question: "Choose the correct word: 'Their going to the store.'",
            options: [
                "Their going to the store.",
                "There going to the store.",
                "They're going to the store.",
                "Theyre going to the store."
            ],
            correct: 2,
            explanation: "'They're' is the contraction of 'they are'"
        },
        {
            id: 16,
            question: "What is an antonym for 'difficult'?",
            options: ["hard", "easy", "challenging", "tough"],
            correct: 1,
            explanation: "'Easy' is the opposite of 'difficult'"
        },
        {
            id: 17,
            question: "Which sentence has correct subject-verb agreement?",
            options: [
                "The books on the shelf is dusty.",
                "The books on the shelf are dusty.",
                "The books on the shelf was dusty.",
                "The books on the shelf be dusty."
            ],
            correct: 1,
            explanation: "Plural subject 'books' requires plural verb 'are'"
        },
        {
            id: 18,
            question: "What is the purpose of a topic sentence?",
            options: [
                "To end the paragraph",
                "To introduce the main idea",
                "To provide an example",
                "To add details"
            ],
            correct: 1,
            explanation: "A topic sentence introduces the main idea of a paragraph"
        },
        {
            id: 19,
            question: "Choose the correct comparative form: 'This book is _____ than that one.'",
            options: ["good", "better", "best", "more good"],
            correct: 1,
            explanation: "'Better' is the comparative form of 'good'"
        },
        {
            id: 20,
            question: "Which is a complete sentence?",
            options: [
                "Running in the park.",
                "Because it was raining.",
                "The dog barked loudly.",
                "After the game ended."
            ],
            correct: 2,
            explanation: "A complete sentence has a subject and predicate and expresses a complete thought"
        },
        {
            id: 21,
            question: "What does the prefix 'un-' mean in 'unhappy'?",
            options: ["very", "not", "before", "after"],
            correct: 1,
            explanation: "The prefix 'un-' means 'not'"
        },
        {
            id: 22,
            question: "Choose the correct word: 'I _____ to the store yesterday.'",
            options: ["go", "goes", "went", "going"],
            correct: 2,
            explanation: "'Went' is the past tense of 'go'"
        },
        {
            id: 23,
            question: "Which sentence uses a metaphor?",
            options: [
                "The sun is like a golden ball.",
                "Time is money.",
                "She runs as fast as a cheetah.",
                "The wind howled through the trees."
            ],
            correct: 1,
            explanation: "A metaphor directly compares two things without using 'like' or 'as'"
        },
        {
            id: 24,
            question: "What is the plural of 'child'?",
            options: ["childs", "childes", "children", "childrens"],
            correct: 2,
            explanation: "'Children' is the irregular plural of 'child'"
        },
        {
            id: 25,
            question: "Choose the correct word: 'The effect/affect of the medicine was immediate.'",
            options: ["effect", "affect", "both work", "neither works"],
            correct: 0,
            explanation: "'Effect' is a noun meaning result; 'affect' is a verb"
        },
        {
            id: 26,
            question: "What is a compound sentence?",
            options: [
                "A sentence with one independent clause",
                "A sentence with two or more independent clauses",
                "A sentence with a dependent clause",
                "A very long sentence"
            ],
            correct: 1,
            explanation: "A compound sentence has two or more independent clauses joined by a conjunction"
        },
        {
            id: 27,
            question: "Which word is an adverb?",
            options: ["quick", "quickly", "quickness", "quicker"],
            correct: 1,
            explanation: "'Quickly' modifies a verb and ends in -ly, making it an adverb"
        },
        {
            id: 28,
            question: "Choose the correct word: 'I have _____ books than you.'",
            options: ["less", "fewer", "lesser", "few"],
            correct: 1,
            explanation: "Use 'fewer' with countable nouns like books"
        },
        {
            id: 29,
            question: "What is the purpose of a conclusion paragraph?",
            options: [
                "To introduce new ideas",
                "To summarize and wrap up the essay",
                "To provide background information",
                "To ask questions"
            ],
            correct: 1,
            explanation: "A conclusion summarizes main points and provides closure"
        },
        {
            id: 30,
            question: "Which sentence uses correct capitalization?",
            options: [
                "I visited New york City.",
                "I visited new York city.",
                "I visited New York City.",
                "I visited new york city."
            ],
            correct: 2,
            explanation: "Proper nouns like city names should be capitalized"
        }
    ],
    science: [
        {
            id: 1,
            question: "What is the basic unit of life?",
            options: ["Atom", "Cell", "Molecule", "Organ"],
            correct: 1,
            explanation: "The cell is the smallest unit of life that can function independently"
        },
        {
            id: 2,
            question: "What is the chemical formula for water?",
            options: ["H2O", "CO2", "O2", "H2O2"],
            correct: 0,
            explanation: "Water consists of two hydrogen atoms and one oxygen atom (H2O)"
        },
        {
            id: 3,
            question: "What process do plants use to make their own food?",
            options: ["Respiration", "Photosynthesis", "Digestion", "Fermentation"],
            correct: 1,
            explanation: "Photosynthesis converts light energy into chemical energy in the form of glucose"
        },
        {
            id: 4,
            question: "What is the center of an atom called?",
            options: ["Electron", "Proton", "Nucleus", "Neutron"],
            correct: 2,
            explanation: "The nucleus is the central core of an atom containing protons and neutrons"
        },
        {
            id: 5,
            question: "What type of rock is formed from cooled lava or magma?",
            options: ["Sedimentary", "Metamorphic", "Igneous", "Limestone"],
            correct: 2,
            explanation: "Igneous rocks form when molten rock cools and solidifies"
        },
        {
            id: 6,
            question: "What is the powerhouse of the cell?",
            options: ["Nucleus", "Ribosome", "Mitochondria", "Chloroplast"],
            correct: 2,
            explanation: "Mitochondria produce ATP, the cell's energy currency"
        },
        {
            id: 7,
            question: "What gas do humans exhale?",
            options: ["Oxygen", "Nitrogen", "Carbon dioxide", "Hydrogen"],
            correct: 2,
            explanation: "Humans breathe in oxygen and exhale carbon dioxide"
        },
        {
            id: 8,
            question: "What is the speed of light in a vacuum?",
            options: ["300,000 km/s", "150,000 km/s", "500,000 km/s", "100,000 km/s"],
            correct: 0,
            explanation: "Light travels at approximately 300,000 kilometers per second in a vacuum"
        },
        {
            id: 9,
            question: "What is the largest organ in the human body?",
            options: ["Heart", "Liver", "Skin", "Brain"],
            correct: 2,
            explanation: "The skin is the largest organ, covering the entire body"
        },
        {
            id: 10,
            question: "What is the pH of a neutral solution?",
            options: ["0", "7", "14", "10"],
            correct: 1,
            explanation: "A pH of 7 is neutral; below 7 is acidic, above 7 is basic"
        },
        {
            id: 11,
            question: "What force keeps planets in orbit around the sun?",
            options: ["Magnetism", "Friction", "Gravity", "Inertia"],
            correct: 2,
            explanation: "Gravity is the attractive force between masses"
        },
        {
            id: 12,
            question: "What is the most abundant gas in Earth's atmosphere?",
            options: ["Oxygen", "Carbon dioxide", "Nitrogen", "Hydrogen"],
            correct: 2,
            explanation: "Nitrogen makes up about 78% of Earth's atmosphere"
        },
        {
            id: 13,
            question: "What is the process by which water changes from liquid to gas?",
            options: ["Condensation", "Evaporation", "Precipitation", "Freezing"],
            correct: 1,
            explanation: "Evaporation is the process of liquid water becoming water vapor"
        },
        {
            id: 14,
            question: "How many chromosomes do humans have in each body cell?",
            options: ["23", "46", "92", "12"],
            correct: 1,
            explanation: "Humans have 46 chromosomes (23 pairs) in each body cell"
        },
        {
            id: 15,
            question: "What is the smallest particle of an element?",
            options: ["Molecule", "Atom", "Proton", "Electron"],
            correct: 1,
            explanation: "An atom is the smallest unit of an element that retains its properties"
        },
        {
            id: 16,
            question: "What type of energy is stored in food?",
            options: ["Kinetic", "Chemical", "Thermal", "Nuclear"],
            correct: 1,
            explanation: "Food contains chemical energy stored in molecular bonds"
        },
        {
            id: 17,
            question: "What is the main function of red blood cells?",
            options: [
                "Fight infection",
                "Carry oxygen",
                "Clot blood",
                "Produce hormones"
            ],
            correct: 1,
            explanation: "Red blood cells transport oxygen from lungs to body tissues"
        },
        {
            id: 18,
            question: "What is the boiling point of water at sea level?",
            options: ["0°C", "50°C", "100°C", "150°C"],
            correct: 2,
            explanation: "Water boils at 100°C (212°F) at standard atmospheric pressure"
        },
        {
            id: 19,
            question: "What is the study of heredity called?",
            options: ["Ecology", "Genetics", "Anatomy", "Physiology"],
            correct: 1,
            explanation: "Genetics is the study of genes and heredity"
        },
        {
            id: 20,
            question: "What layer of Earth is liquid?",
            options: ["Crust", "Mantle", "Outer core", "Inner core"],
            correct: 2,
            explanation: "The outer core is liquid iron and nickel"
        },
        {
            id: 21,
            question: "What is the primary source of energy for Earth?",
            options: ["The Moon", "The Sun", "Geothermal heat", "Wind"],
            correct: 1,
            explanation: "The Sun provides energy for photosynthesis and drives weather patterns"
        },
        {
            id: 22,
            question: "What is Newton's first law of motion?",
            options: [
                "F = ma",
                "An object in motion stays in motion unless acted upon",
                "For every action there is an equal and opposite reaction",
                "Energy cannot be created or destroyed"
            ],
            correct: 1,
            explanation: "The law of inertia states objects maintain their state of motion"
        },
        {
            id: 23,
            question: "What organelle controls cell activities?",
            options: ["Mitochondria", "Nucleus", "Cell membrane", "Ribosome"],
            correct: 1,
            explanation: "The nucleus contains DNA and controls cell functions"
        },
        {
            id: 24,
            question: "What is the freezing point of water?",
            options: ["−10°C", "0°C", "10°C", "32°C"],
            correct: 1,
            explanation: "Water freezes at 0°C (32°F) at standard pressure"
        },
        {
            id: 25,
            question: "What is the process of cell division called?",
            options: ["Meiosis", "Mitosis", "Both A and B", "Photosynthesis"],
            correct: 2,
            explanation: "Both mitosis (body cells) and meiosis (sex cells) are types of cell division"
        },
        {
            id: 26,
            question: "What is the chemical symbol for gold?",
            options: ["Go", "Gd", "Au", "Ag"],
            correct: 2,
            explanation: "Au comes from the Latin word 'aurum' meaning gold"
        },
        {
            id: 27,
            question: "What is the largest planet in our solar system?",
            options: ["Saturn", "Jupiter", "Neptune", "Uranus"],
            correct: 1,
            explanation: "Jupiter is the largest planet with a diameter of about 143,000 km"
        },
        {
            id: 28,
            question: "What is the function of chlorophyll in plants?",
            options: [
                "Store water",
                "Absorb light for photosynthesis",
                "Produce oxygen",
                "Transport nutrients"
            ],
            correct: 1,
            explanation: "Chlorophyll absorbs light energy, primarily blue and red wavelengths"
        },
        {
            id: 29,
            question: "What is an ecosystem?",
            options: [
                "A single organism",
                "A group of the same species",
                "Living and non-living things interacting",
                "Only plants in an area"
            ],
            correct: 2,
            explanation: "An ecosystem includes all organisms and their physical environment"
        },
        {
            id: 30,
            question: "What is the unit of electrical resistance?",
            options: ["Volt", "Ampere", "Ohm", "Watt"],
            correct: 2,
            explanation: "Resistance is measured in ohms (Ω)"
        }
    ],
    social: [
        {
            id: 1,
            question: "How many branches are there in the U.S. government?",
            options: ["Two", "Three", "Four", "Five"],
            correct: 1,
            explanation: "The three branches are Legislative, Executive, and Judicial"
        },
        {
            id: 2,
            question: "What is the supreme law of the United States?",
            options: [
                "The Declaration of Independence",
                "The Constitution",
                "The Bill of Rights",
                "The Federalist Papers"
            ],
            correct: 1,
            explanation: "The U.S. Constitution is the supreme law of the land"
        },
        {
            id: 3,
            question: "Who was the first President of the United States?",
            options: [
                "Thomas Jefferson",
                "John Adams",
                "George Washington",
                "Benjamin Franklin"
            ],
            correct: 2,
            explanation: "George Washington served as the first U.S. President from 1789-1797"
        },
        {
            id: 4,
            question: "What economic system is based on supply and demand?",
            options: ["Socialism", "Communism", "Capitalism", "Feudalism"],
            correct: 2,
            explanation: "Capitalism is a market economy driven by supply and demand"
        },
        {
            id: 5,
            question: "What year did the United States declare independence?",
            options: ["1774", "1775", "1776", "1777"],
            correct: 2,
            explanation: "The Declaration of Independence was signed on July 4, 1776"
        },
        {
            id: 6,
            question: "How many amendments are in the Bill of Rights?",
            options: ["5", "10", "15", "27"],
            correct: 1,
            explanation: "The Bill of Rights consists of the first 10 amendments"
        },
        {
            id: 7,
            question: "What is the longest river in the world?",
            options: ["Amazon", "Nile", "Mississippi", "Yangtze"],
            correct: 1,
            explanation: "The Nile River in Africa is approximately 6,650 km long"
        },
        {
            id: 8,
            question: "Who has the power to declare war in the U.S.?",
            options: ["President", "Congress", "Supreme Court", "Military"],
            correct: 1,
            explanation: "Only Congress has the constitutional power to declare war"
        },
        {
            id: 9,
            question: "What is inflation?",
            options: [
                "Decrease in prices",
                "Increase in prices over time",
                "Stable prices",
                "Government spending"
            ],
            correct: 1,
            explanation: "Inflation is the rate at which the general level of prices rises"
        },
        {
            id: 10,
            question: "What document freed the slaves in Confederate states?",
            options: [
                "The Constitution",
                "The Bill of Rights",
                "The Emancipation Proclamation",
                "The Gettysburg Address"
            ],
            correct: 2,
            explanation: "Lincoln issued the Emancipation Proclamation in 1863"
        },
        {
            id: 11,
            question: "What is the capital of the United States?",
            options: ["New York", "Philadelphia", "Washington, D.C.", "Boston"],
            correct: 2,
            explanation: "Washington, D.C. has been the U.S. capital since 1800"
        },
        {
            id: 12,
            question: "How many senators does each state have?",
            options: ["One", "Two", "Three", "It varies"],
            correct: 1,
            explanation: "Each state has exactly two senators regardless of population"
        },
        {
            id: 13,
            question: "What is the largest continent?",
            options: ["Africa", "Asia", "North America", "Europe"],
            correct: 1,
            explanation: "Asia is the largest continent by both area and population"
        },
        {
            id: 14,
            question: "What war was fought between the North and South in the U.S.?",
            options: [
                "Revolutionary War",
                "War of 1812",
                "Civil War",
                "World War I"
            ],
            correct: 2,
            explanation: "The Civil War (1861-1865) was fought between Union and Confederate states"
        },
        {
            id: 15,
            question: "What is the main purpose of the United Nations?",
            options: [
                "World trade",
                "International peace and cooperation",
                "Space exploration",
                "Environmental protection"
            ],
            correct: 1,
            explanation: "The UN was founded to maintain international peace and security"
        },
        {
            id: 16,
            question: "What is GDP?",
            options: [
                "Government Debt Product",
                "Gross Domestic Product",
                "General Development Plan",
                "Global Distribution Process"
            ],
            correct: 1,
            explanation: "GDP measures the total value of goods and services produced in a country"
        },
        {
            id: 17,
            question: "Who wrote the Declaration of Independence?",
            options: [
                "George Washington",
                "Benjamin Franklin",
                "Thomas Jefferson",
                "John Adams"
            ],
            correct: 2,
            explanation: "Thomas Jefferson was the primary author of the Declaration"
        },
        {
            id: 18,
            question: "What ocean is on the West Coast of the United States?",
            options: ["Atlantic", "Pacific", "Indian", "Arctic"],
            correct: 1,
            explanation: "The Pacific Ocean borders the western United States"
        },
        {
            id: 19,
            question: "What is the minimum voting age in the United States?",
            options: ["16", "18", "21", "25"],
            correct: 1,
            explanation: "The 26th Amendment set the voting age at 18"
        },
        {
            id: 20,
            question: "What is a tariff?",
            options: [
                "A type of tax on imports",
                "A trade agreement",
                "A government subsidy",
                "A business regulation"
            ],
            correct: 0,
            explanation: "A tariff is a tax imposed on imported goods"
        },
        {
            id: 21,
            question: "What movement fought for women's right to vote?",
            options: [
                "Civil Rights Movement",
                "Suffrage Movement",
                "Labor Movement",
                "Progressive Movement"
            ],
            correct: 1,
            explanation: "The Women's Suffrage Movement led to the 19th Amendment in 1920"
        },
        {
            id: 22,
            question: "What is the largest desert in the world?",
            options: ["Sahara", "Arabian", "Gobi", "Antarctic"],
            correct: 3,
            explanation: "Antarctica is technically the largest desert (very low precipitation)"
        },
        {
            id: 23,
            question: "How long is a U.S. presidential term?",
            options: ["2 years", "4 years", "6 years", "8 years"],
            correct: 1,
            explanation: "A presidential term is 4 years, with a maximum of two terms"
        },
        {
            id: 24,
            question: "What is the study of maps called?",
            options: ["Geography", "Cartography", "Topography", "Geology"],
            correct: 1,
            explanation: "Cartography is the science and art of making maps"
        },
        {
            id: 25,
            question: "What was the main cause of the Great Depression?",
            options: [
                "World War I",
                "Stock market crash of 1929",
                "Drought",
                "High taxes"
            ],
            correct: 1,
            explanation: "The 1929 stock market crash triggered the Great Depression"
        },
        {
            id: 26,
            question: "What is the highest court in the United States?",
            options: [
                "District Court",
                "Appeals Court",
                "Supreme Court",
                "Federal Court"
            ],
            correct: 2,
            explanation: "The Supreme Court is the highest judicial authority"
        },
        {
            id: 27,
            question: "What is the imaginary line that divides Earth into Northern and Southern hemispheres?",
            options: ["Prime Meridian", "Equator", "Tropic of Cancer", "Arctic Circle"],
            correct: 1,
            explanation: "The Equator is at 0° latitude"
        },
        {
            id: 28,
            question: "What is scarcity in economics?",
            options: [
                "Unlimited resources",
                "Limited resources vs. unlimited wants",
                "Equal distribution",
                "Government control"
            ],
            correct: 1,
            explanation: "Scarcity is the fundamental economic problem of limited resources"
        },
        {
            id: 29,
            question: "Who was the leader of the Civil Rights Movement?",
            options: [
                "Malcolm X",
                "Rosa Parks",
                "Martin Luther King Jr.",
                "Frederick Douglass"
            ],
            correct: 2,
            explanation: "Dr. Martin Luther King Jr. was a prominent civil rights leader"
        },
        {
            id: 30,
            question: "What are the first words of the U.S. Constitution?",
            options: [
                "When in the course",
                "We the People",
                "Four score and seven",
                "In Congress assembled"
            ],
            correct: 1,
            explanation: "The Constitution begins with 'We the People of the United States'"
        }
    ]
};

// Test configurations
const testConfig = {
    math: { duration: 115 * 60, totalQuestions: 30 },
    language: { duration: 150 * 60, totalQuestions: 30 },
    science: { duration: 90 * 60, totalQuestions: 30 },
    social: { duration: 70 * 60, totalQuestions: 30 }
};

// Application state
let currentState = {
    subject: null,
    mode: null,
    questions: [],
    currentQuestionIndex: 0,
    answers: {},
    flagged: new Set(),
    startTime: null,
    endTime: null,
    timerInterval: null,
    timeRemaining: 0
};

// DOM Elements
const welcomeScreen = document.getElementById('welcomeScreen');
const examScreen = document.getElementById('examScreen');
const resultsScreen = document.getElementById('resultsScreen');

// Initialize app
document.addEventListener('DOMContentLoaded', () => {
    setupEventListeners();
});

function setupEventListeners() {
    // Subject selection
    document.querySelectorAll('.subject-card').forEach(card => {
        card.addEventListener('click', () => selectSubject(card.dataset.subject));
    });

    // Mode selection
    document.querySelectorAll('.mode-card').forEach(card => {
        card.addEventListener('click', () => selectMode(card.dataset.mode));
    });

    // Back button
    document.querySelector('.btn-back').addEventListener('click', () => {
        document.querySelector('.subject-selection').classList.remove('hidden');
        document.querySelector('.test-mode-selection').classList.add('hidden');
    });

    // Exam controls
    document.getElementById('btnPrevious').addEventListener('click', previousQuestion);
    document.getElementById('btnNext').addEventListener('click', nextQuestion);
    document.getElementById('btnFlag').addEventListener('click', toggleFlag);
    document.getElementById('btnEndExam').addEventListener('click', confirmEndExam);

    // Results actions
    document.getElementById('btnRetakeExam').addEventListener('click', retakeExam);
    document.getElementById('btnNewSubject').addEventListener('click', () => {
        showScreen('welcome');
        resetState();
    });
}

function selectSubject(subject) {
    currentState.subject = subject;
    document.querySelector('.subject-selection').classList.add('hidden');
    document.querySelector('.test-mode-selection').classList.remove('hidden');
}

function selectMode(mode) {
    currentState.mode = mode;
    startExam();
}

function startExam() {
    // Load questions
    currentState.questions = shuffleArray([...questionBank[currentState.subject]]);
    currentState.currentQuestionIndex = 0;
    currentState.answers = {};
    currentState.flagged = new Set();
    currentState.startTime = Date.now();

    // Set up timer
    if (currentState.mode === 'timed') {
        currentState.timeRemaining = testConfig[currentState.subject].duration;
        startTimer();
    }

    // Update UI
    updateExamHeader();
    buildQuestionNavigator();
    showScreen('exam');
    displayQuestion();

    // Enter fullscreen mode for exam
    enterFullscreen();
}

function updateExamHeader() {
    const subjectNames = {
        math: 'Mathematical Reasoning',
        language: 'Reasoning Through Language Arts',
        science: 'Science',
        social: 'Social Studies'
    };

    const modeNames = {
        practice: 'Practice Mode',
        timed: 'Timed Test',
        review: 'Review Mode'
    };

    document.getElementById('examSubject').textContent = subjectNames[currentState.subject];
    document.getElementById('examMode').textContent = modeNames[currentState.mode];

    // Show/hide timer
    const timerDisplay = document.getElementById('timerDisplay');
    if (currentState.mode === 'timed') {
        timerDisplay.style.display = 'flex';
    } else {
        timerDisplay.style.display = 'none';
    }
}

function startTimer() {
    updateTimerDisplay();
    currentState.timerInterval = setInterval(() => {
        currentState.timeRemaining--;
        updateTimerDisplay();

        if (currentState.timeRemaining <= 0) {
            clearInterval(currentState.timerInterval);
            endExam();
        }
    }, 1000);
}

function updateTimerDisplay() {
    const minutes = Math.floor(currentState.timeRemaining / 60);
    const seconds = currentState.timeRemaining % 60;
    const timeString = `${minutes}:${seconds.toString().padStart(2, '0')}`;
    document.getElementById('timeRemaining').textContent = timeString;

    const timerElement = document.getElementById('timerDisplay');
    if (currentState.timeRemaining < 300) { // Less than 5 minutes
        timerElement.classList.add('danger');
    } else if (currentState.timeRemaining < 600) { // Less than 10 minutes
        timerElement.classList.add('warning');
        timerElement.classList.remove('danger');
    } else {
        timerElement.classList.remove('warning', 'danger');
    }
}

function buildQuestionNavigator() {
    const grid = document.getElementById('questionGrid');
    grid.innerHTML = '';

    currentState.questions.forEach((q, index) => {
        const btn = document.createElement('button');
        btn.className = 'question-nav-btn';
        btn.textContent = index + 1;
        btn.addEventListener('click', () => goToQuestion(index));
        grid.appendChild(btn);
    });

    updateQuestionNavigator();
}

function updateQuestionNavigator() {
    const buttons = document.querySelectorAll('.question-nav-btn');
    buttons.forEach((btn, index) => {
        btn.classList.remove('current', 'answered', 'flagged');

        if (index === currentState.currentQuestionIndex) {
            btn.classList.add('current');
        } else if (currentState.flagged.has(index)) {
            btn.classList.add('flagged');
        } else if (currentState.answers[index] !== undefined) {
            btn.classList.add('answered');
        }
    });

    // Update progress
    const answered = Object.keys(currentState.answers).length;
    const total = currentState.questions.length;
    document.getElementById('progressText').textContent = `${answered}/${total}`;
    document.getElementById('progressFill').style.width = `${(answered / total) * 100}%`;
    document.getElementById('answeredCount').textContent = `${answered} answered`;
    document.getElementById('flaggedCount').textContent = `${currentState.flagged.size} flagged`;
}

function displayQuestion() {
    const question = currentState.questions[currentState.currentQuestionIndex];

    // Update question number
    document.getElementById('currentQuestionNum').textContent = currentState.currentQuestionIndex + 1;
    document.getElementById('totalQuestions').textContent = currentState.questions.length;

    // Update question text
    document.getElementById('questionText').textContent = question.question;

    // Update answer options
    const optionsContainer = document.getElementById('answerOptions');
    optionsContainer.innerHTML = '';

    question.options.forEach((option, index) => {
        const optionDiv = document.createElement('div');
        optionDiv.className = 'answer-option';

        // Check if this option is selected
        if (currentState.answers[currentState.currentQuestionIndex] === index) {
            optionDiv.classList.add('selected');
        }

        // In review mode, show correct/incorrect
        if (currentState.mode === 'review') {
            if (index === question.correct) {
                optionDiv.classList.add('correct');
            } else if (currentState.answers[currentState.currentQuestionIndex] === index) {
                optionDiv.classList.add('incorrect');
            }
        }

        const label = document.createElement('div');
        label.className = 'answer-label';
        label.textContent = String.fromCharCode(65 + index); // A, B, C, D

        const text = document.createElement('div');
        text.className = 'answer-text';
        text.textContent = option;

        optionDiv.appendChild(label);
        optionDiv.appendChild(text);

        // Only allow selection if not in review mode
        if (currentState.mode !== 'review') {
            optionDiv.addEventListener('click', () => selectAnswer(index));
        }

        optionsContainer.appendChild(optionDiv);
    });

    // Update flag button
    const flagBtn = document.getElementById('btnFlag');
    if (currentState.flagged.has(currentState.currentQuestionIndex)) {
        flagBtn.classList.add('flagged');
    } else {
        flagBtn.classList.remove('flagged');
    }

    // Update navigation buttons
    document.getElementById('btnPrevious').disabled = currentState.currentQuestionIndex === 0;

    const nextBtn = document.getElementById('btnNext');
    if (currentState.currentQuestionIndex === currentState.questions.length - 1) {
        nextBtn.textContent = 'Finish Exam';
    } else {
        nextBtn.textContent = 'Next →';
    }

    updateQuestionNavigator();
}

function selectAnswer(optionIndex) {
    currentState.answers[currentState.currentQuestionIndex] = optionIndex;
    displayQuestion();
}

function toggleFlag() {
    const index = currentState.currentQuestionIndex;
    if (currentState.flagged.has(index)) {
        currentState.flagged.delete(index);
    } else {
        currentState.flagged.add(index);
    }
    displayQuestion();
}

function previousQuestion() {
    if (currentState.currentQuestionIndex > 0) {
        currentState.currentQuestionIndex--;
        displayQuestion();
    }
}

function nextQuestion() {
    if (currentState.currentQuestionIndex < currentState.questions.length - 1) {
        currentState.currentQuestionIndex++;
        displayQuestion();
    } else {
        confirmEndExam();
    }
}

function goToQuestion(index) {
    currentState.currentQuestionIndex = index;
    displayQuestion();
}

function confirmEndExam() {
    const answered = Object.keys(currentState.answers).length;
    const total = currentState.questions.length;

    if (answered < total) {
        const unanswered = total - answered;
        if (!confirm(`You have ${unanswered} unanswered question(s). Are you sure you want to end the exam?`)) {
            return;
        }
    }

    endExam();
}

function endExam() {
    if (currentState.timerInterval) {
        clearInterval(currentState.timerInterval);
    }

    currentState.endTime = Date.now();

    // Exit fullscreen when exam ends
    exitFullscreen();

    showResults();
}

function showResults() {
    // Calculate score
    let correct = 0;
    currentState.questions.forEach((question, index) => {
        if (currentState.answers[index] === question.correct) {
            correct++;
        }
    });

    const total = currentState.questions.length;
    const percentage = Math.round((correct / total) * 100);

    // Update score display
    document.getElementById('scorePercentage').textContent = `${percentage}%`;
    document.getElementById('correctCount').textContent = correct;
    document.getElementById('incorrectCount').textContent = total - correct;
    document.getElementById('totalQuestionsResult').textContent = total;

    // Calculate time taken
    const timeTaken = Math.floor((currentState.endTime - currentState.startTime) / 1000);
    const minutes = Math.floor(timeTaken / 60);
    const seconds = timeTaken % 60;
    document.getElementById('timeTaken').textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

    // Animate score circle
    const circumference = 2 * Math.PI * 90;
    const offset = circumference - (percentage / 100) * circumference;
    const scoreCircle = document.getElementById('scoreCircle');
    scoreCircle.style.strokeDashoffset = offset;

    // Build answer review
    const reviewContainer = document.getElementById('answerReview');
    reviewContainer.innerHTML = '';

    currentState.questions.forEach((question, index) => {
        const isCorrect = currentState.answers[index] === question.correct;
        const userAnswer = currentState.answers[index];

        const reviewItem = document.createElement('div');
        reviewItem.className = `review-item ${isCorrect ? 'correct' : 'incorrect'}`;

        reviewItem.innerHTML = `
            <div class="review-header">
                <span class="review-question-num">Question ${index + 1}</span>
                <span class="review-status ${isCorrect ? 'correct' : 'incorrect'}">
                    ${isCorrect ? '✓ Correct' : '✗ Incorrect'}
                </span>
            </div>
            <div class="review-question">${question.question}</div>
            <div class="review-answers">
                <div class="review-answer">
                    <strong>Your answer:</strong> ${userAnswer !== undefined ? question.options[userAnswer] : 'Not answered'}
                </div>
                <div class="review-answer">
                    <strong>Correct answer:</strong> ${question.options[question.correct]}
                </div>
                ${question.explanation ? `<div class="review-answer"><strong>Explanation:</strong> ${question.explanation}</div>` : ''}
            </div>
        `;

        reviewContainer.appendChild(reviewItem);
    });

    showScreen('results');
}

function retakeExam() {
    startExam();
}

function resetState() {
    currentState = {
        subject: null,
        mode: null,
        questions: [],
        currentQuestionIndex: 0,
        answers: {},
        flagged: new Set(),
        startTime: null,
        endTime: null,
        timerInterval: null,
        timeRemaining: 0
    };

    document.querySelector('.subject-selection').classList.remove('hidden');
    document.querySelector('.test-mode-selection').classList.add('hidden');
}

function showScreen(screen) {
    welcomeScreen.classList.remove('active');
    examScreen.classList.remove('active');
    resultsScreen.classList.remove('active');

    switch (screen) {
        case 'welcome':
            welcomeScreen.classList.add('active');
            break;
        case 'exam':
            examScreen.classList.add('active');
            break;
        case 'results':
            resultsScreen.classList.add('active');
            break;
    }
}

function shuffleArray(array) {
    const newArray = [...array];
    for (let i = newArray.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [newArray[i], newArray[j]] = [newArray[j], newArray[i]];
    }
    return newArray;
}

// Fullscreen functionality
function enterFullscreen() {
    const elem = document.documentElement;

    if (elem.requestFullscreen) {
        elem.requestFullscreen().catch(err => {
            console.log('Fullscreen request failed:', err);
        });
    } else if (elem.webkitRequestFullscreen) { // Safari
        elem.webkitRequestFullscreen();
    } else if (elem.msRequestFullscreen) { // IE11
        elem.msRequestFullscreen();
    }

    // Add event listeners for fullscreen changes
    document.addEventListener('fullscreenchange', handleFullscreenChange);
    document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
    document.addEventListener('msfullscreenchange', handleFullscreenChange);

    // Prevent right-click during exam
    document.addEventListener('contextmenu', preventContextMenu);

    // Warn before leaving page
    window.addEventListener('beforeunload', handleBeforeUnload);

    // Detect tab visibility changes
    document.addEventListener('visibilitychange', handleVisibilityChange);
}

function exitFullscreen() {
    if (document.exitFullscreen) {
        document.exitFullscreen();
    } else if (document.webkitExitFullscreen) {
        document.webkitExitFullscreen();
    } else if (document.msExitFullscreen) {
        document.msExitFullscreen();
    }

    // Remove event listeners
    document.removeEventListener('fullscreenchange', handleFullscreenChange);
    document.removeEventListener('webkitfullscreenchange', handleFullscreenChange);
    document.removeEventListener('msfullscreenchange', handleFullscreenChange);
    document.removeEventListener('contextmenu', preventContextMenu);
    window.removeEventListener('beforeunload', handleBeforeUnload);
    document.removeEventListener('visibilitychange', handleVisibilityChange);
}

function handleFullscreenChange() {
    const isFullscreen = document.fullscreenElement ||
        document.webkitFullscreenElement ||
        document.msFullscreenElement;

    // If user exits fullscreen during exam, warn them
    if (!isFullscreen && examScreen.classList.contains('active')) {
        const warning = confirm(
            '⚠️ EXAM MODE VIOLATION\n\n' +
            'You have exited fullscreen mode during the exam.\n' +
            'This would be flagged in a real testing environment.\n\n' +
            'Click OK to return to fullscreen mode.\n' +
            'Click Cancel to end the exam.'
        );

        if (warning) {
            enterFullscreen();
        } else {
            endExam();
        }
    }
}

function preventContextMenu(e) {
    e.preventDefault();
    return false;
}

function handleBeforeUnload(e) {
    if (examScreen.classList.contains('active')) {
        e.preventDefault();
        e.returnValue = 'Your exam is in progress. Are you sure you want to leave?';
        return e.returnValue;
    }
}

function handleVisibilityChange() {
    if (document.hidden && examScreen.classList.contains('active')) {
        // Log that user switched tabs (in real exam, this would be reported)
        console.warn('Tab switch detected during exam - this would be flagged in real testing');
    }
}

// Disable common keyboard shortcuts during exam
function preventShortcuts(e) {
    // Prevent F11 (fullscreen toggle)
    if (e.key === 'F11') {
        e.preventDefault();
    }
    // Prevent Ctrl+W (close tab)
    if (e.ctrlKey && e.key === 'w') {
        e.preventDefault();
    }
    // Prevent Ctrl+T (new tab)
    if (e.ctrlKey && e.key === 't') {
        e.preventDefault();
    }
    // Prevent Alt+Tab attempt (Alt+F4)
    if (e.altKey && e.key === 'F4') {
        e.preventDefault();
    }
}

// Add keyboard shortcut prevention when exam starts
document.addEventListener('keydown', preventShortcuts);
