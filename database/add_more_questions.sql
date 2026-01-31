-- Add more Science and Social Studies questions
USE exam_management;

-- Science Questions (Subject ID = 3)
INSERT INTO questions (subject_id, question_text, option_a, option_b, option_c, option_d, correct_answer, explanation, difficulty, year, created_by) VALUES
(3, 'What is the basic unit of life?', 'Atom', 'Cell', 'Molecule', 'Organ', 'B', 'The cell is the smallest unit that can carry out all the processes of life', 'easy', 2025, 1),
(3, 'What is photosynthesis?', 'Breaking down food', 'Converting light to energy', 'Cell division', 'DNA replication', 'B', 'Photosynthesis is the process by which plants convert light energy into chemical energy', 'medium', 2025, 1),
(3, 'Which planet is closest to the Sun?', 'Venus', 'Earth', 'Mercury', 'Mars', 'C', 'Mercury is the closest planet to the Sun', 'easy', 2025, 1),
(3, 'What is H2O?', 'Hydrogen', 'Oxygen', 'Water', 'Carbon dioxide', 'C', 'H2O is the chemical formula for water', 'easy', 2025, 1),
(3, 'What is the powerhouse of the cell?', 'Nucleus', 'Mitochondria', 'Ribosome', 'Chloroplast', 'B', 'Mitochondria produce energy (ATP) for the cell', 'medium', 2025, 1),
(3, 'What force pulls objects toward Earth?', 'Magnetism', 'Friction', 'Gravity', 'Inertia', 'C', 'Gravity is the force that attracts objects with mass toward each other', 'easy', 2025, 1),
(3, 'What is the speed of light?', '300,000 km/s', '150,000 km/s', '500,000 km/s', '100,000 km/s', 'A', 'Light travels at approximately 300,000 kilometers per second', 'medium', 2025, 1),
(3, 'What is DNA?', 'A protein', 'A carbohydrate', 'Genetic material', 'A lipid', 'C', 'DNA (deoxyribonucleic acid) carries genetic information', 'medium', 2025, 1),
(3, 'How many bones are in the human body?', '186', '206', '226', '246', 'B', 'An adult human has 206 bones', 'medium', 2025, 1),
(3, 'What gas do plants absorb from the atmosphere?', 'Oxygen', 'Nitrogen', 'Carbon dioxide', 'Hydrogen', 'C', 'Plants absorb CO2 for photosynthesis', 'easy', 2025, 1),
(3, 'What is the largest organ in the human body?', 'Heart', 'Brain', 'Liver', 'Skin', 'D', 'The skin is the largest organ, covering the entire body', 'medium', 2024, 1),
(3, 'What type of rock is formed by cooling lava?', 'Sedimentary', 'Metamorphic', 'Igneous', 'Limestone', 'C', 'Igneous rocks form from cooled magma or lava', 'medium', 2024, 1),
(3, 'What is the chemical symbol for gold?', 'Go', 'Gd', 'Au', 'Ag', 'C', 'Au comes from the Latin word aurum', 'easy', 2024, 1),
(3, 'How many chambers does the human heart have?', '2', '3', '4', '5', 'C', 'The heart has four chambers: two atria and two ventricles', 'easy', 2024, 1),
(3, 'What is the boiling point of water at sea level?', '90°C', '100°C', '110°C', '120°C', 'B', 'Water boils at 100°C (212°F) at sea level', 'easy', 2024, 1);

-- Social Studies Questions (Subject ID = 4)
INSERT INTO questions (subject_id, question_text, option_a, option_b, option_c, option_d, correct_answer, explanation, difficulty, year, created_by) VALUES
(4, 'Who was the first President of the United States?', 'Thomas Jefferson', 'George Washington', 'John Adams', 'Benjamin Franklin', 'B', 'George Washington served as the first U.S. President from 1789-1797', 'easy', 2025, 1),
(4, 'In what year did the United States declare independence?', '1774', '1775', '1776', '1777', 'C', 'The Declaration of Independence was signed on July 4, 1776', 'easy', 2025, 1),
(4, 'How many branches of government does the U.S. have?', '2', '3', '4', '5', 'B', 'The three branches are Executive, Legislative, and Judicial', 'easy', 2025, 1),
(4, 'What is the capital of the United States?', 'New York', 'Philadelphia', 'Washington D.C.', 'Boston', 'C', 'Washington D.C. has been the capital since 1800', 'easy', 2025, 1),
(4, 'How many amendments are in the Bill of Rights?', '5', '10', '15', '20', 'B', 'The Bill of Rights consists of the first 10 amendments', 'easy', 2025, 1),
(4, 'What is the supreme law of the land?', 'The Declaration', 'The Constitution', 'The Bill of Rights', 'Federal law', 'B', 'The U.S. Constitution is the supreme law', 'medium', 2025, 1),
(4, 'Who wrote the Declaration of Independence?', 'George Washington', 'Benjamin Franklin', 'Thomas Jefferson', 'John Adams', 'C', 'Thomas Jefferson was the primary author', 'medium', 2025, 1),
(4, 'What are the first three words of the Constitution?', 'We the People', 'In Congress Assembled', 'We hold these', 'When in the', 'A', 'The Constitution begins with "We the People"', 'easy', 2025, 1),
(4, 'How many senators does each state have?', '1', '2', '3', '4', 'B', 'Each state has two senators in the U.S. Senate', 'easy', 2025, 1),
(4, 'What is an amendment?', 'A law', 'A change to the Constitution', 'A court decision', 'A presidential order', 'B', 'An amendment is a change or addition to the Constitution', 'medium', 2025, 1),
(4, 'Who is Commander in Chief of the military?', 'Secretary of Defense', 'Chairman of Joint Chiefs', 'The President', 'Congress', 'C', 'The President serves as Commander in Chief', 'easy', 2024, 1),
(4, 'How long is a U.S. Senator''s term?', '2 years', '4 years', '6 years', '8 years', 'C', 'Senators serve six-year terms', 'medium', 2024, 1),
(4, 'What is the economic system of the United States?', 'Socialism', 'Communism', 'Capitalism', 'Feudalism', 'C', 'The U.S. has a capitalist/market economy', 'medium', 2024, 1),
(4, 'What ocean is on the West Coast of the United States?', 'Atlantic', 'Pacific', 'Indian', 'Arctic', 'B', 'The Pacific Ocean borders the West Coast', 'easy', 2024, 1),
(4, 'What is the highest court in the United States?', 'District Court', 'Appeals Court', 'Supreme Court', 'Federal Court', 'C', 'The Supreme Court is the highest court', 'easy', 2024, 1);
